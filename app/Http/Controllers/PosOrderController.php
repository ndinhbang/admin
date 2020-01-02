<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\PosOrderRequest;
use App\Http\Resources\PosOrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PosOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  PosOrderRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(PosOrderRequest $request)
    {
        $orders = Order::with([
            'place',
            'creator',
            'customer',
            'table',
            'items' => function ($query) {
                $query->where('parent_id', 0);
            },
            'items.children.product',
            'items.product',
        ])
            ->filter(new OrderFilter($request))
            ->orderBy('orders.id', 'desc')
            ->get();
        return PosOrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PosOrderRequest  $request
     * @return PosOrderResource
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(PosOrderRequest $request)
    {
        $data         = $request->all();
        $items        = $data['items'] ?? [];
        $productUuids = $this->getProductUuidOfAllOrderItems($items);
        $products     = Product::whereIn('uuid', $productUuids)
            ->get();
        if ( $products->isEmpty()
            || $products->count() != count($productUuids) ) {
            throw new \InvalidArgumentException('Malformed data.');
        }
        $user  = $request->user();
        $order = DB::transaction(function () use ($products, $data, $user) {
            // tao order
            $now       = Carbon::now();
            $customer  = getBindVal('__customer');
            $orderData = array_merge($this->prepareOrderData($data), [
                'uuid'       => nanoId(),
                'place_id'   => currentPlace()->id,
                'creator_id' => $user->id,
                'kind'       => getOrderKind($data['kind'], true),
                'code'       => $data['code'] ?? null,
                'year'       => $now->year,
                'month'      => $now->month,
                'day'        => $now->day,
            ]);
            // calculate items price
            $products->load('supplies');
            $calculatedItemsData = $this->calculateItemsData($data['items'], $products->keyBy('uuid'));
            $calculatedOrderData = $this->calculateOrderData($orderData, $calculatedItemsData);
            // save new order
            $order = Order::create($calculatedOrderData);
            // save new items
            $this->syncOrderItems($calculatedItemsData, $order);
            //nếu bán thành công
            if ( $order->is_completed || $order->is_paid ) {
                // trù kho
                $this->subtractInventory($products, $data['items']);
                if ( $order->paid ) {
                    // tao phieu thu
                    $order->createVoucher($data['payment_method'] ?? 'cash');
                }
                if ( $customer ) {
                    // Cập nhật thông tin tổng quan cho account
                    $customer->updateOrdersStats();
                }
            }
            return $order;
        }, 5);
        unset($products);
        $order->load([
            'place',
            'table',
            'customer',
            'items' => function ($query) {
                $query->where('parent_id', 0);
            },
            'items.children.product',
            'items.product',
        ]);
        return new PosOrderResource($order);
    }

    /**
     * @param  array  $items
     * @return   array
     */
    private function getProductUuidOfAllOrderItems(array $items)
    {
        if ( empty($items) ) {
            throw new \InvalidArgumentException('Order require items');
        }
        $uuids = [];
        foreach ( $items as $item ) {
            // throw error here if price not found
            $uuids[ $item['product_uuid'] ] = 1;
            $children                       = $item['children'] ?? [];
            if ( empty($item['children']) ) {
                continue;
            }
            foreach ( $children as $child ) {
                // throw error here if price not found
                $uuids[ $child['product_uuid'] ] = 1;
            }
        }
        return array_keys($uuids);
    }

    /**
     * @param  array  $requestData
     * @return array
     */
    private function prepareOrderData(array $requestData)
    {
        $table    = getBindVal('__table');
        $customer = getBindVal('__customer');
        return [
            'table_id'              => $table->id ?? null,
            'customer_id'           => $customer->id ?? 0,
            'note'                  => $requestData['note'] ?? '',
            'reason'                => $requestData['reason'] ?? '',
            'card_name'             => $requestData['card_name'] ?? '',
            'total_eater'           => (int) $requestData['total_eater'],
            'total_dish'            => (int) $requestData['total_dish'],
            'received_amount'       => (int) $requestData['received_amount'],
            'discount_amount'       => (int) $requestData['discount_amount'],
            'is_returned'           => (bool) $requestData['is_returned'],
            'is_canceled'           => (bool) $requestData['is_canceled'],
            'is_served'             => (bool) $requestData['is_served'],
            // update later
            'amount'                => 0,
            'discount_items_amount' => 0,
            'paid'                  => 0,
            'debt'                  => 0,
            'is_paid'               => false,
            'is_completed'          => false,
        ];
    }

    /**
     * @param  array                           $items
     * @param  \Illuminate\Support\Collection  $keyedProducts
     * @return array
     */
    private function calculateItemsData(array $items, Collection $keyedProducts)
    {
        $result = [];
        foreach ( $items as $item ) {
            $itemProduct = $keyedProducts[ $item['product_uuid'] ];
            // tiền vốn
            $itemTotalBuyingPrice    = 0;
            $itemTotalAvgBuyingPrice = 0;
            if ( $itemProduct->can_stock ) {
                foreach ( $itemProduct->supplies as $key => $supply ) {
                    $itemTotalBuyingPrice    += $supply->pivot->quantity * $supply->price_in;
                    $itemTotalAvgBuyingPrice += $supply->pivot->quantity * $supply->price_avg_in;
                }
            }
            // tinh gia
            $itemQuantity       = (int) $item['quantity'];
            $itemDiscountAmount = $item['discount_amount'] ?? 0;
            $itemBasePrice      = $itemQuantity * $itemProduct->price;
            // giá sản phẩm (sau khi giảm giá)
            $itemSimplePrice = $itemBasePrice - $itemDiscountAmount;
            // tổng giá của các sản phẩm bán kèm
            $itemChildrenPrice = 0;
            // tổng giá giảm trên các sản phẩm bán kèm
            $itemChildDiscountAmount = 0;
            $children                = $item['children'] ?? [];
            $datas                   = [];
            if ( !empty($children) ) {
                $datas = $this->calculateItemsData($children, $keyedProducts);
                foreach ( $datas as $data ) {
                    $itemChildrenPrice       += $data['total_price'];
                    $itemChildDiscountAmount += $data['total_discount_amount'];
                }
            }
            // tổng giảm giá (bao gồm giảm giá của sản phẩm hiện tại và các sản phẩm bán kèm)
            $itemTotalDiscountAmount = $itemDiscountAmount + $itemChildDiscountAmount;
            // tổng giá sau giảm giá của sản phẩm hiện tại và các sản phẩm bán kèm
            $itemTotalPrice          = $itemSimplePrice + $itemChildrenPrice;
            $result[ $item['uuid'] ] = [
                // calculated
                'product_id'               => $itemProduct->id,
                'quantity'                 => $itemQuantity,
                'discount_amount'          => $itemDiscountAmount,
                'children_discount_amount' => $itemChildDiscountAmount,
                'simple_price'             => $itemSimplePrice,
                'children_price'           => $itemChildrenPrice,
                'total_price'              => $itemTotalPrice,
                'total_buying_price'       => $itemTotalBuyingPrice,
                'total_buying_avg_price'   => $itemTotalAvgBuyingPrice,
                // data from request
                'added_qty'                => $item['added_qty'] ?? 0,
                'note'                     => $item['note'] ?? '',
                'canceled'                 => $item['canceled'],
                'completed'                => $item['completed'],
                'delivering'               => $item['delivering'],
                'done'                     => $item['done'],
                'doing'                    => $item['doing'],
                'accepted'                 => $item['accepted'],
                'pending'                  => $item['pending'],
                'discount_id'              => $item['discount_id'] ?? 0,
                // need to remove when create or update item
                'base_price'               => $itemBasePrice,
                'total_discount_amount'    => $itemTotalDiscountAmount,
                'child_data'               => $datas,
            ];
        }
        return $result;
    }

    /**
     * @param  array  $orderData
     * @param  array  $calculatedItemsData
     * @return array
     */
    private function calculateOrderData(array $orderData, array $calculatedItemsData)
    {
        $orderBaseAmount = 0;
        foreach ( $calculatedItemsData as $item ) {
            $orderData['discount_items_amount'] += $item['total_discount_amount'];
            $orderBaseAmount                    += $item['total_price'];
        }
        $orderData['amount'] = $orderBaseAmount - $orderData['discount_amount'];
        if ( $orderData['received_amount'] > 0 ) {
            if ( $orderData['received_amount'] >= $orderData['amount'] ) {
                $orderData['paid'] = $orderData['amount'];
            } else {
                $orderData['paid'] = $orderData['received_amount'];
                $orderData['debt'] = $orderData['amount'] - $orderData['received_amount'];
            }
        }
        // Trả 1 phần cũng là đã trả, nhưng chưa hoàn thành đơn hàng
        $orderData['is_paid']      = $orderData['paid'] > 0;
        $orderData['is_completed'] = $orderData['paid'] > 0 && ( $orderData['paid'] == $orderData['amount'] );
        return $orderData;
    }

    /**
     * @param  array                           $data
     * @param  \App\Models\Order               $order
     * @param  \Illuminate\Support\Collection  $keyedItems
     * @param  int                             $parentItemId
     */
    private function syncOrderItems(array $data, Order $order, Collection $keyedItems = null, $parentItemId = 0)
    {
        $changes = [
            // mảng các item uuid tạo mới
            'attached' => [],
            // mảng các item uuid bị xóa
            'detached' => [],
            // mảng các item uuid cập nhật
            'updated'  => [],
        ];
        $current = array_keys($data);
        $existed = is_null($keyedItems) ? [] : $keyedItems->keys()
            ->all();
        if ( empty($existed) ) {
            $changes['attached'] = array_flip($current);
        } else {
            $changes['attached'] = array_flip(array_diff($current, $existed)); // => [uuid] => index
            $changes['detached'] = array_flip(array_diff($existed, $current));
            $changes['updated']  = array_flip(array_intersect($current, $existed));
        }
        // phần trăm giảm giá trên từng sản phẩm
        $discountOrderPercent = ( $order->discount_amount * 100 ) / ( $order->amount + $order->discount_amount );
        foreach ( $data as $itemUuid => $calculatedItemData ) {
            $discountOrderAmount = round(( $calculatedItemData['total_price'] * $discountOrderPercent ) / 100);
            $pareparedArr        = array_merge($this->prepareOrderItemData($calculatedItemData), [
                'place_id'              => $order->place_id,
                'order_id'              => $order->id,
                'discount_order_amount' => $discountOrderAmount,
            ]);
            if ( isset($changes['detached'][ $itemUuid ]) ) {
                $deletedItem = $keyedItems->get($itemUuid);
                OrderItem::where('id', $deletedItem->id)
                    ->where('parent_id', $deletedItem->id)
                    ->delete();
                continue;
            }
            $parentIdOfChild = 0;
            $originItem      = null;
            if ( isset($changes['updated'][ $itemUuid ]) ) {
                // todo: cap nhat added_qty (khong su dung so lieu duoi local) ?
                $originItem = $keyedItems->get($itemUuid); // instance of OrderItem
                OrderItem::where('id', $originItem->id)
                    ->update(array_merge($pareparedArr, [
                        'added_qty' => $originItem->added_qty + (int) $pareparedArr['added_qty'],
                        'parent_id' => $parentItemId,
                    ]));
            }
            if ( isset($changes['attached'][ $itemUuid ]) ) {
                $originItem = OrderItem::create(array_merge($pareparedArr, [
                    'uuid'      => nanoId(),
                    'parent_id' => $parentItemId,
                ]));
            }
            if ( !empty($calculatedItemData['child_data']) ) {
                $keyedChildItems = new Collection();
                if ( $originItem ) {
                    $keyedChildItems = $originItem->children->keyBy('uuid');
                }
                $this->syncOrderItems($calculatedItemData['child_data'], $order, $keyedChildItems,
                    $originItem->id ?? 0);
            }
        }
    }

    /**
     * @param  array  $calculatedItemData
     * @return array
     */
    private function prepareOrderItemData(array $calculatedItemData)
    {
        return Arr::only($calculatedItemData, [
            'quantity',
            'discount_amount',
            'children_discount_amount',
            'simple_price',
            'children_price',
            'total_price',
            'total_buying_price',
            'total_buying_avg_price',
            'product_id',
            // data from request
            'note',
            'canceled',
            'completed',
            'delivering',
            'done',
            'doing',
            'accepted',
            'pending',
            'discount_id',
            'added_qty',
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection  $products
     * @param  array                           $items
     * @throws \Exception
     */
    private function subtractInventory(Collection $products, array $items)
    {
        $canStockProducts = $products
            ->where('can_stock', true)
            ->pipe(function ($filtered) {
                return $filtered->load([
                    'supplies.availableStocks' => function ($query) {
                        $query->where('inventory_orders.status', 1) // don nhap da hoan thanh
                        ->orderBy('inventory_orders.id', 'asc');
                    },
                ]);
            })
            ->keyBy('uuid');
//        dump($canStockProducts->toArray());
        foreach ( $items as $item ) {
            if ( is_null($product = $canStockProducts->get($item['product_uuid'])) ) {
                continue;
            }
            $supplies = $product->supplies ?? new Collection();
            if ( $supplies->isEmpty() ) {
                throw new \Exception("Chưa khai báo nguyên liệu cho sản phẩm {$product->name}.");
            };
            // tổng số lượng sản phẩm trong order
            $productQuantity = (int) $item['quantity'];
            $now             = Carbon::now()
                ->format('Y-m-d H:i:s');
            foreach ( $supplies as $supply ) {
                // dump($supply);
                $stocks = $supply->availableStocks ?? new Collection();
                // throw error if empty
                if ( $stocks->isEmpty() ) {
                    throw new \Exception("Không đủ nguyên liệu: \"{$supply->name}\" cho sản phẩm \"{$product->name}\" trong kho.");
                };
                // số lượng nguyên liệu / 1 sản phẩm
                $supplyQuantity = $supply->pivot->quantity;
                //tổng số lương trừ kho
                $outQuantity = $supplyQuantity * $productQuantity;
                // lặp các lần nhập kho
                foreach ( $stocks as $stock ) {
                    if ( $outQuantity <= 0 ) {
                        break;
                    }
                    // nếu tồn kho nhiều hơn tổng trừ kho
                    if ( $stock->pivot->remain >= $outQuantity ) {
                        // ... thực hiện trừ kho
                        $supply->stocks()
                            ->updateExistingPivot($stock->id, [
                                'remain'     => $stock->pivot->remain - $outQuantity,
                                'updated_at' => $now,
                            ]);
                        $outQuantity = 0;
                        break;
                    }
                    // nếu tổng trừ kho nhiều hơn tồn kho trong lần nhập kho hiện tại
                    // số lượng trừ kho còn lại
                    $outQuantity = $outQuantity - $stock->pivot->remain;
                    // ... trừ hết số lượng tồn
                    $supply->stocks()
                        ->updateExistingPivot($stock->id, [
                            'remain'     => 0,
                            'updated_at' => $now,
                        ]);
                } // end of stocks
                // nếu tống trừ kho lớn hơn tổng tồn kho
                if ( $outQuantity > 0 ) {
                    throw new \Exception("\"{$supply->name}\" tồn kho không đủ số lượng bán của sản phẩm \"{$product->name}\" ");
                }
            } // end of supplies
            // unload redundant relations
            $product->unsetRelation('supplies');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PosOrderRequest  $request
     * @param  Order            $order
     * @return PosOrderResource
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(PosOrderRequest $request, Order $order)
    {
        if ( isOrderClosed($order) ) {
            return response()->json([
                'message' => 'Order đã đóng không thể cập nhật',
            ], 403);
        }
        $data         = $request->all();
        $items        = $data['items'] ?? [];
        $productUuids = $this->getProductUuidOfAllOrderItems($items);
        $products     = Product::whereIn('uuid', $productUuids)
            ->get();
        if ( $products->isEmpty()
            || $products->count() != count($productUuids) ) {
            throw new \InvalidArgumentException('Malformed data.');
        }
        $order = DB::transaction(function () use ($products, $data, $order) {
            $orderData = $this->prepareOrderData($data);
            $products->load([ 'supplies' ]);
            $calculatedItemsData = $this->calculateItemsData($data['items'], $products->keyBy('uuid'));
            $calculatedOrderData = $this->calculateOrderData($orderData, $calculatedItemsData);
            $oldPaid             = $order->paid;
            // update order
            $order->update($calculatedOrderData);
            // save items
            $order->load([
                'items' => function ($query) {
                    $query->where('parent_id', 0);
                },
                'items.children',
            ]);
            $keyedItems = $order->items->keyBy('uuid') ?? new Collection();
            $this->syncOrderItems($calculatedItemsData, $order, $keyedItems);
            //nếu bán thành công
            if ( $order->is_completed || $order->is_paid ) {
                // trù kho
                $this->subtractInventory($products, $data['items']);
                if ( $order->paid
                    && $order->paid > $oldPaid ) {
                    // tao phieu thu
                    $order->createVoucher($data['payment_method'] ?? 'cash');
                }
            }
            return $order;
        }, 5);
        unset($products);
        $order->load([
            'place',
            'table',
            'customer',
            'items' => function ($query) {
                $query->where('parent_id', 0);
            },
            'items.children.product',
            'items.product',
        ]);
        return new PosOrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  Order  $order
     * @return PosOrderResource
     */
    public function show(Order $order)
    {
        $order->load([
            'place',
            'customer',
            'table',
            'items' => function ($query) {
                $query->where('parent_id', 0);
            },
            'items.children.product',
            'items.product',
        ]);
        return new PosOrderResource($order);
    }

    /**
     * @param  \App\Http\Requests\PosOrderRequest  $request
     * @param  \App\Models\Order                   $order
     * @return \App\Http\Resources\PosOrderResource
     * @throws \Exception
     * @throws \Throwable
     */
    public function printed(PosOrderRequest $request, Order $order)
    {
        $order->load([
            'items' => function ($query) {
                $query->where('parent_id', 0);
            },
            'items.children.product',
            'items.product',
        ]);

        DB::transaction(function () use ($order) {
            if (!$order->items->isEmpty()) {
                foreach ($order->items as $item) {
                    $item->added_qty = 0;
                    $item->save();
                    if (!$item->children->isEmpty()) {
                        foreach ($item->children as $child) {
                            $child->added_qty = 0;
                            $child->save();
                        }
                    }
                }
            }
        }, 5);


        $order->load([
            'place',
            'table',
            'customer',
            'items' => function ($query) {
                $query->where('parent_id', 0);
            },
            'items.children.product',
            'items.product',
        ]);
        return new PosOrderResource($order);
    }

    /**
     * @param  \App\Http\Requests\PosOrderRequest  $request
     * @param  \App\Models\Order                   $order
     * @return \App\Http\Resources\PosOrderResource
     * @throws \Exception
     */
    public function canceled(PosOrderRequest $request, Order $order)
    {
        $isCanceled = $request->is_canceled ?? false;
        $reason     = $request->reason ?? '';
        if ( $isCanceled ) {
            if ( !$reason ) {
                throw new \Exception('ERROR: Chưa nhập lý do hủy');
            }
            if ( isOrderClosed($order) ) {
                throw new \Exception('ERROR: Order đã đóng không thể hủy');
            }
            $order->is_canceled = 1;
            $order->reason      = $reason;
            $order->save();
        }
        $order->load([
            'place',
            'table',
            'customer',
            'items' => function ($query) {
                $query->where('parent_id', 0);
            },
            'items.children.product',
            'items.product',
        ]);
        return new PosOrderResource($order);
    }
}
