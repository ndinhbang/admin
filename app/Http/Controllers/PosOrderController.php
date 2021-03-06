<?php

namespace App\Http\Controllers;

use App\Events\OrderCanceled;
use App\Events\OrderPaid;
use App\Events\OrderUpdated;
use App\Events\PromotionChanged;
use App\Http\Filters\OrderFilter;
use App\Http\Requests\PosOrderRequest;
use App\Http\Resources\PosOrderResource;
use App\Http\Resources\PosOrdersCollection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Segment;
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
     * @return \App\Http\Resources\PosOrdersCollection
     * @throws \Exception
     */
    public function index(PosOrderRequest $request)
    {
        $orders = Order::with(
            [
                'creator',
                'customer',
                'table.area',
                'items' => function ($query) {
                    $query->where('parent_id', 0);
                },
                'items.children.product.category',
                'items.product.category',
            ]
        )
            ->filter(new OrderFilter($request))
            ->orderBy('orders.id', 'desc')
            ->get();
        return ( new PosOrdersCollection($orders) )->using(
            [
                'place_uuid' => currentPlace()->uuid,
            ]
        );
    }

    /**
     * List uuid of inactive orders
     *
     * @param  \App\Http\Requests\PosOrderRequest  $request
     * @return array
     */
    public function inactive(PosOrderRequest $request)
    {
        $orders = Order::withTrashed()
            ->where('is_paid', 1)
            ->orWhere('is_returned', 1)
            ->orWhere('is_completed', 1)
            ->orWhere('is_canceled', 1)
            ->filter(new OrderFilter($request))
            ->orderBy('orders.id', 'desc')
            ->get([ 'uuid' ]);
        return $orders->toArray();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PosOrderRequest  $request
     * @return false|string
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(PosOrderRequest $request)
    {
        $customer     = getBindVal('__customer');
        $table        = getBindVal('__table');
        $promotions   = getBindVal('__keyedPromotions') ?? collect([]);
        $data         = $request->all();
        $items        = $data[ 'items' ] ?? [];
        $productUuids = $this->getProductUuidOfAllOrderItems($items);
        $products     = Product::whereIn('uuid', $productUuids)->get();
        if ( $products->count() != count($productUuids) ) {
            throw new \InvalidArgumentException('Malformed data.');
        }
        $user  = $request->user();
        $order = DB::transaction(
            function () use ($products, $data, $user, $promotions) {
                // tao order
                $now       = Carbon::now();
                $customer  = getBindVal('__customer');
                $orderData = array_merge(
                    $this->prepareOrderData($data),
                    [
                        'uuid'       => nanoId(),
                        'place_id'   => currentPlace()->id,
                        'creator_id' => $user->id,
                        'code'       => $data[ 'code' ] ?? null,
                        'year'       => $now->year,
                        'month'      => $now->month,
                        'day'        => $now->day,
                    ]
                );
                // calculate items price
                $calculatedItemsData = [];
                if ( !$products->isEmpty() ) {
                    $products->load('supplies');
                    $calculatedItemsData = $this->calculateItemsData($data[ 'items' ], $products->keyBy('uuid'));
                }
                $calculatedOrderData = $this->calculateOrderData($orderData, $calculatedItemsData);
                // save new order
                $order = Order::create($calculatedOrderData);
                // sync promotions
                $order->syncPromotions($promotions, $data);
                // save new items
                $this->syncOrderItems($order, $calculatedItemsData);
                //nếu bán thành công
                if ( $order->is_completed || $order->is_paid ) {
                    // trù kho
                    $this->subtractInventory($order, $products, $data[ 'items' ]);
                    if ( $order->paid ) {
                        // tao phieu thu
                        $order->createVoucher($data[ 'payment_method' ] ?? 'cash');
                    }
                    if ( $customer ) {
                        // Cập nhật thông tin tổng quan cho account
                        $customer->updateOrdersStats();
                        //todo: attach segment
                        $pivotData = [];
                        $segments  = Segment::cursor();
                        foreach ( $segments as $segment ) {
                            if ( $customer->isSatisfiedAllConditions($segment->conditions ?? []) ) {
                                $pivotData[] = $segment->id;
                            }
                        }
                        // todo: cronjob to remove unsatified customers
                        $customer->segments()->syncWithoutDetaching($pivotData);
                    }
                    // chỉ trừ số lượng khi thu tiền
                    if ( !$promotions->isEmpty() ) {
                        Promotion::whereIn('id', $promotions->pluck('id')->all())
                            ->update([ 'applied_qty' => DB::raw('applied_qty + 1') ]);
                    }
                }
                return $order;
            },
            5
        );
        unset($products);
        $order->load(
            [
                'promotions',
                'items' => function ($query) {
                    $query->where('parent_id', 0);
                },
                'items.children.product.category',
                'items.product.category',
            ]
        );
        $usingArr = [
            'place_uuid'    => currentPlace()->uuid,
            'table_uuid'    => $table->uuid ?? null,
            'table_name'    => $table->name ?? '',
            'area_name'     => $table->area->name ?? '',
            'customer_uuid' => $customer->uuid ?? null,
            'customer_name' => $customer->name ?? '',
            'customer_code' => $customer->code ?? '',
        ];
        $resource = ( new PosOrderResource($order) )->using($usingArr);
        $response = $resource->toResponse($request);
        // broadcast event
        if ( $order->is_paid ) {
            broadcast(new OrderPaid($order, currentPlace()))->toOthers();
            foreach ( $promotions as $promotion ) {
                if ( $promotion->is_limited ) {
                    broadcast(new PromotionChanged($promotion, currentPlace()->uuid));
                }
            }
        } else {
            broadcast(new OrderUpdated($response->getData()))->toOthers();
        }
        // return json string
        return $response->getContent();
    }

    /**
     * @param  array  $items
     * @return   array
     */
    private function getProductUuidOfAllOrderItems(array $items = [])
    {
        if ( empty($items) ) {
            return [];
        }
        $uuids = [];
        foreach ( $items as $item ) {
            // throw error here if price not found
            $uuids[ $item[ 'product_uuid' ] ] = 1;
            $children                         = $item[ 'children' ] ?? [];
            if ( empty($item[ 'children' ]) ) {
                continue;
            }
            foreach ( $children as $child ) {
                // throw error here if price not found
                $uuids[ $child[ 'product_uuid' ] ] = 1;
            }
        }
        return array_keys($uuids);
    }

    /**
     * @param  array  $data
     * @return array
     */
    private function prepareOrderData(array $data)
    {
        $table    = getBindVal('__table');
        $customer = getBindVal('__customer');
        return [
            'kind'                  => getOrderKind($data[ 'kind' ], true),
            'table_id'              => $table->id ?? null,
            'customer_id'           => $customer->id ?? 0,
            'note'                  => $data[ 'note' ] ?? '',
            'reason'                => $data[ 'reason' ] ?? '',
            'card_name'             => $data[ 'card_name' ] ?? '',
            'total_eater'           => (int) $data[ 'total_eater' ],
            'total_dish'            => (int) $data[ 'total_dish' ],
            'received_amount'       => (int) $data[ 'received_amount' ],
            'discount_amount'       => (int) $data[ 'discount_amount' ],
            'is_returned'           => (bool) $data[ 'is_returned' ],
            'is_canceled'           => (bool) $data[ 'is_canceled' ],
            'is_served'             => (bool) $data[ 'is_served' ],
            'promotion_automated'   => (bool) $data[ 'promotion_automated' ],
            'promotion_uuid'        => $data[ 'promotion_uuid' ] ?? null,
            'promotion_id'          =>
                !empty($data[ 'promotion_uuid' ])
                    ? getBindVal('__keyedPromotions')->get($data[ 'promotion_uuid' ])->id
                    : null,
            // update later
            'amount'                => 0,
            'discount_items_amount' => 0,
            'paid'                  => 0,
            'debt'                  => 0,
            'is_paid'               => $data[ 'is_paid' ] ?? false,
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
        if ( empty($items) ) {
            return $result;
        }
        foreach ( $items as $item ) {
            $itemProduct = $keyedProducts[ $item[ 'product_uuid' ] ];
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
            $itemQuantity       = (float) $item[ 'quantity' ];
            $itemDiscountAmount = $item[ 'discount_amount' ] ?? 0;
            $itemBasePrice      = $itemQuantity * $itemProduct->price;
            // giá sản phẩm (sau khi giảm giá)
            $itemSimplePrice = $itemBasePrice - $itemDiscountAmount;
            // tổng giá của các sản phẩm bán kèm
            $itemChildrenPrice = 0;
            // tổng giá giảm trên các sản phẩm bán kèm
            $itemChildDiscountAmount = 0;
            $children                = $item[ 'children' ] ?? [];
            $datas                   = [];
            if ( !empty($children) ) {
                $datas = $this->calculateItemsData($children, $keyedProducts);
                foreach ( $datas as $data ) {
                    $itemChildrenPrice       += ($data[ 'total_price' ] * $itemQuantity);
                    $itemChildDiscountAmount += ($data[ 'total_discount_amount' ] * $itemQuantity);
                }
            }
            // tổng giảm giá (bao gồm giảm giá của sản phẩm hiện tại và các sản phẩm bán kèm)
            $itemTotalDiscountAmount = $itemDiscountAmount + $itemChildDiscountAmount;
            // tổng giá sau giảm giá của sản phẩm hiện tại và các sản phẩm bán kèm
            $itemTotalPrice = $itemSimplePrice + $itemChildrenPrice;
            $isPaused = $item[ 'is_paused' ] ?? false;
            $timeIn   = Carbon::parse($item[ 'time_in' ] ?? 'now');
            $timeOut  = $isPaused ? Carbon::parse($item[ 'time_out' ] ?? 'now') : Carbon::now();
            $result[ $item[ 'uuid' ] ] = [
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
                'promotion_id'             =>
                    !empty($item[ 'promotion_uuid' ])
                        ? getBindVal('__keyedPromotions')->get($item[ 'promotion_uuid' ])->id
                        : null,
                'promotion_uuid'           => $item[ 'promotion_uuid' ] ?? null,
                'product_price'            => $item[ 'product_price' ],
                'printed_qty'              => $item[ 'added_qty' ] ?? 0,
                'note'                     => $item[ 'note' ] ?? '',
                'canceled'                 => $item[ 'canceled' ],
                'completed'                => $item[ 'completed' ],
                'delivering'               => $item[ 'delivering' ],
                'done'                     => $item[ 'done' ],
                'doing'                    => $item[ 'doing' ],
                'accepted'                 => $item[ 'accepted' ],
                'pending'                  => $item[ 'pending' ],
                'time_used'                => Carbon::now()->diffInMinutes(Carbon::parse($timeIn)),
                'time_in'                  => $timeIn,
                'time_out'                 => $timeOut,
                'is_paused'                => $isPaused,
                'price_by_time'            => $item[ 'price_by_time' ],
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
    private function calculateOrderData(array $orderData, array $calculatedItemsData = [])
    {
        $orderBaseAmount = 0;
        if ( !empty($calculatedItemsData) ) {
            foreach ( $calculatedItemsData as $item ) {
                $orderData[ 'discount_items_amount' ] += $item[ 'total_discount_amount' ];
                $orderBaseAmount                      += $item[ 'total_price' ];
            }
        }
        if ( $orderData[ 'discount_amount' ] < 0 ) {
            $orderData[ 'discount_amount' ] = 0;
        }
        // giảm giá tối đa bằng tổng giá hàng bán
        if ( $orderBaseAmount < $orderData[ 'discount_amount' ] ) {
            $orderData[ 'discount_amount' ] = $orderBaseAmount;
        }
        $orderData[ 'amount' ] = $orderBaseAmount - $orderData[ 'discount_amount' ];
        if ( $orderData[ 'received_amount' ] >= $orderData[ 'amount' ] ) {
            $orderData[ 'paid' ] = $orderData[ 'amount' ];
        } else {
            $orderData[ 'paid' ] = $orderData[ 'received_amount' ];
            $orderData[ 'debt' ] = $orderData[ 'amount' ] - $orderData[ 'received_amount' ];
        }
        // Trả 1 phần cũng là đã trả, nhưng chưa hoàn thành đơn hàng
        // $orderData['is_paid']      = $orderData['paid'] > 0;
        $orderData[ 'is_completed' ] = $orderData[ 'is_paid' ] && $orderData[ 'paid' ] > 0 && ( $orderData[ 'paid' ] == $orderData[ 'amount' ] );
        return $orderData;
    }

    /**
     * @param  array                           $keyedData     mảng các items mới, với key là item uuid
     * @param  \App\Models\Order               $order         order instance
     * @param  \Illuminate\Support\Collection  $keyedItems    mảng các items cũ
     * @param  int                             $parentItemId  id của item cha
     */
    private function syncOrderItems(
        Order $order,
        array $keyedData = [],
        Collection $keyedItems = null,
        $parentItemId = 0
    ) {
        $changes = [
            // mảng các item uuid tạo mới
            'attached' => [],
            // mảng các item uuid bị xóa
            'detached' => [],
            // mảng các item uuid cập nhật
            'updated'  => [],
        ];
        $current = empty($keyedData) ? [] : array_keys($keyedData);
        $existed = is_null($keyedItems) || $keyedItems->isEmpty() ? [] : $keyedItems->keys()->all();
        if ( empty($current) && empty($existed) ) {
            return; // skip
        }
        $changes[ 'attached' ] = array_flip(array_diff($current, $existed)); // => [uuid] => index
        $changes[ 'detached' ] = array_flip(array_diff($existed, $current));
        $changes[ 'updated' ]  = array_flip(array_intersect($current, $existed));
        // phần trăm giảm giá trên từng sản phẩm
        $discountOrderPercent = 0;
        if ( !empty($keyedData) && $order->amount ) {
            $discountOrderPercent = ( $order->discount_amount * 100 ) / ( $order->amount + $order->discount_amount );
        }
        // Xóa item cũ không có trong mảng item mới
        if ( !empty($changes[ 'detached' ]) ) {
            foreach ( $changes[ 'detached' ] as $detachedUuids => $uselessValue ) {
                $deletedItem = $keyedItems->get($detachedUuids);
                OrderItem::where('id', $deletedItem->id)
                    ->orWhere('parent_id', $deletedItem->id)
                    ->delete();
            }
        }
        // Thêm mới item
        if ( !empty($changes[ 'attached' ]) ) {
            foreach ( $changes[ 'attached' ] as $attachedUuids => $uselessValue ) {
                $calculatedData      = $keyedData[ $attachedUuids ];
                $discountOrderAmount = round(( $calculatedData[ 'total_price' ] * $discountOrderPercent ) / 100);
                $pareparedArr        = array_merge(
                    $this->prepareOrderItemData($calculatedData),
                    [
                        'place_id'              => $order->place_id,
                        'order_id'              => $order->id,
                        'discount_order_amount' => $discountOrderAmount,
                    ]
                );
                $newOrder            = OrderItem::create(
                    array_merge(
                        $pareparedArr,
                        [
                            'uuid'      => nanoId(),
                            'parent_id' => $parentItemId,
                        ]
                    )
                );
                $this->syncOrderItems(
                    $order,
                    $calculatedData[ 'child_data' ],
                    null,
                    $newOrder->id
                );
            }
        }
        // Cập nhật items
        if ( !empty($changes[ 'updated' ]) ) {
            foreach ( $changes[ 'updated' ] as $updatedUuids => $uselessValue ) {
                $originItem          = $keyedItems->get($updatedUuids);
                $calculatedData      = $keyedData[ $updatedUuids ];
                $discountOrderAmount = round(( $calculatedData[ 'total_price' ] * $discountOrderPercent ) / 100);
                $pareparedArr        = array_merge(
                    $this->prepareOrderItemData($calculatedData),
                    [
                        'discount_order_amount' => $discountOrderAmount,
                    ]
                );
                OrderItem::where('id', $originItem->id)
                    ->update(
                        array_merge(
                            $pareparedArr,
                            [
                                // số lượng in = số lượng cũ chưa in + số lượng thêm mới
                                'printed_qty' => $originItem->printed_qty + (int) $pareparedArr[ 'printed_qty' ],
                                'parent_id'   => $parentItemId,
                            ]
                        )
                    );
                $this->syncOrderItems(
                    $order,
                    $calculatedData[ 'child_data' ],
                    $originItem->children->keyBy('uuid'),
                    $originItem->id
                );
            }
        }
    }

    /**
     * @param  array  $calculatedItemData
     * @return array
     */
    private function prepareOrderItemData(array $calculatedItemData)
    {
        return Arr::only(
            $calculatedItemData,
            [
                'quantity',
                'discount_amount',
                'children_discount_amount',
                'simple_price',
                'children_price',
                'product_price',
                'total_price',
                'total_buying_price',
                'total_buying_avg_price',
                'product_id',
                'promotion_id',
                'promotion_uuid',
                // data from request
                'note',
                'canceled',
                'completed',
                'delivering',
                'done',
                'doing',
                'accepted',
                'pending',
                'printed_qty',
                'is_paused',
                'time_used',
                'time_in',
                'time_out',
                'price_by_time',
            ]
        );
    }

    /**
     * @param  \App\Models\Order               $order
     * @param  \Illuminate\Support\Collection  $products
     * @param  array                           $item
     * @throws \Exception
     */
    private function subtractItemInventory(Order $order, Collection $products, array $item) {
        if ( is_null($product = $products->get($item[ 'product_uuid' ])) ) {
            return;
            }
            $supplies = $product->supplies ?? new Collection();
            if ( $supplies->isEmpty() ) {
                throw new \Exception("Chưa khai báo nguyên liệu cho sản phẩm {$product->name}.");
            };
            // tổng số lượng sản phẩm trong order
            $productQuantity = (int) $item[ 'quantity' ];
            $now             = Carbon::now()->format('Y-m-d H:i:s');
            foreach ( $supplies as $supply ) {
                // kiếm tra thiết lập cho phép bán khi tồn kho không đủ?
            $configSale = currentPlace()->config_sale ?? null;
                // $printInfo = currentPlace()->print_info;
                // dd($printInfo);
                // số lượng nguyên liệu / 1 sản phẩm
                $supplyQuantity = $supply->pivot->quantity;
                //tổng số lương trừ kho
                $outQuantity = $supplyQuantity * $productQuantity;
                if ( ( !is_null($configSale) && !$configSale[ 'allowOverstock' ] )
                    && $outQuantity > $supply->remain ) {
                    // throw error if empty
                    throw new \Exception(
                        "\"{$supply->name}\" trong kho chỉ còn: {$supply->remain} - không đủ để bán cho sản phẩm \"{$product->name}\"."
                    );
                }
                // thông tin phiếu xuất kho
                $inventoryArr = [
                    'ref_code'    => $order->code,
                    'order_id'    => $order->id,
                    'supply_id'   => $supply->id,
                    'qty_export'  => $outQuantity,
                    'qty_remain'  => $supply->remain - $outQuantity,
                    'total_price' => $item[ 'total_price' ],
                    'price_pu'    => $item[ 'product_price' ],
                    'status'      => 1,
                    'note'        => 'Xuất kho cho đơn hàng ' . $order->code,
                ];
                // thêm phiếu xuất kho
                $order->inventory()->create($inventoryArr);
                $supply->remain = $supply->remain - $outQuantity;
                $supply->save();
            } // end of supplies
            // unload redundant relations
    }

    /**
     * @param  \App\Models\Order               $order
     * @param  \Illuminate\Support\Collection  $products
     * @param  array                           $items
     * @throws \Exception
     */
    private function subtractInventory(Order $order, Collection $products, array $items)
    {
        $canStockProducts = $products
            ->where('can_stock', true)
            ->keyBy('uuid');
        foreach ( $items as $item ) {
            $this->subtractItemInventory($order, $canStockProducts, $item);
            if (empty($item['children'])) continue;
            foreach ($item['children'] as $child) {
                $this->subtractItemInventory($order, $canStockProducts, $child);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PosOrderRequest  $request
     * @param  Order            $order
     * @return false|string
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(PosOrderRequest $request, Order $order)
    {
//        if ( isOrderClosed($order) ) {
//            return response()->json([
//                'message' => 'Order đã thanh toán hoặc hủy không thể cập nhật',
//            ], 403);
//        }
        $customer     = getBindVal('__customer');
        $table        = getBindVal('__table');
        $promotions   = getBindVal('__keyedPromotions') ?? collect([]);
        $data         = $request->all();
        $items        = $data[ 'items' ] ?? [];
        $productUuids = $this->getProductUuidOfAllOrderItems($items);
        $products     = Product::whereIn('uuid', $productUuids)
            ->get();
        if ( $products->isEmpty()
            && $products->count() != count($productUuids) ) {
            throw new \InvalidArgumentException('Malformed data.');
        }
        // Promotion
        $order = DB::transaction(
            function () use ($products, $data, $order, $customer, $promotions) {
                $orderData = $this->prepareOrderData($data);
                $products->load([ 'supplies' ]);
                $calculatedItemsData = $this->calculateItemsData($data[ 'items' ], $products->keyBy('uuid'));
                $calculatedOrderData = $this->calculateOrderData($orderData, $calculatedItemsData);
                $oldPaid             = $order->paid;
                // update order
                $order->update($calculatedOrderData);
                // save items
                $order->load(
                    [
                        'items' => function ($query) {
                            $query->where('parent_id', 0);
                        },
                        'items.children',
                    ]
                );
                $keyedItems = $order->items->keyBy('uuid') ?? new Collection();
                $this->syncOrderItems($order, $calculatedItemsData, $keyedItems);
                // sync promotions
                // sync promotions
                $order->syncPromotions($promotions, $data);
                //nếu bán thành công
                if ( $order->is_completed || $order->is_paid ) {
                    // trù kho
                    $this->subtractInventory($order, $products, $data[ 'items' ]);
                    if ( $order->paid ) {
                        // tao phieu thu
                        $order->createVoucher($data[ 'payment_method' ] ?? 'cash');
                    }
                    if ( $customer ) {
                        // Cập nhật thông tin tổng quan cho account
                        $customer->updateOrdersStats();
                        //todo: attach segment
                        $pivotData = [];
                        $segments  = Segment::cursor();
                        foreach ( $segments as $segment ) {
                            if ( $customer->isSatisfiedAllConditions($segment->conditions ?? []) ) {
                                $pivotData[] = $segment->id;
                            }
                        }
                        $customer->segments()->syncWithoutDetaching($pivotData);
                    }
                    // chỉ trừ số lượng khi thu tiền
                    if ( !$promotions->isEmpty() ) {
                        Promotion::whereIn('id', $promotions->pluck('id')->all())
                            ->update([ 'applied_qty' => DB::raw('applied_qty + 1') ]);
                    }
                }
                return $order;
            },
            5
        );
        unset($products);
        $order->load(
            [
//            'place',
//            'table',
//            'customer',
                'promotions',
                'items' => function ($query) {
                    $query->where('parent_id', 0);
                },
                'items.children.product.category',
                'items.product.category',
            ]
        );
        $usingArr = [
            'place_uuid'    => currentPlace()->uuid,
            'table_uuid'    => $table->uuid ?? null,
            'table_name'    => $table->name ?? '',
            'area_name'     => $table->area->name ?? '',
            'customer_uuid' => $customer->uuid ?? null,
            'customer_name' => $customer->name ?? '',
            'customer_code' => $customer->code ?? '',
        ];
        $resource = ( new PosOrderResource($order) )->using($usingArr);
        $response = $resource->toResponse($request);
        // broadcast event
        if ( $order->is_paid ) {
            broadcast(new OrderPaid($order, currentPlace()))->toOthers();
            foreach ( $promotions as $promotion ) {
                if ( $promotion->is_limited ) {
                    broadcast(new PromotionChanged($promotion, currentPlace()->uuid));
                }
            }
        } else {
            broadcast(new OrderUpdated($response->getData()))->toOthers();
        }
        // return json string
        return $response->getContent();
    }

    /**
     * Display the specified resource.
     *
     * @param  Order  $order
     * @return PosOrderResource
     * @throws \Exception
     */
    public function show(Order $order)
    {
        $order->load(
            [
//            'place',
                'promotions',
                'customer',
                'table.area',
                'items' => function ($query) {
                    $query->where('parent_id', 0);
                },
                'items.children.product.category',
                'items.product.category',
            ]
        );
        return ( new PosOrderResource($order) )->using(
            [
                'place_uuid' => currentPlace()->uuid,
            ]
        );
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
        $order->load(
            [
                'items',
            ]
        );
        DB::transaction(
            function () use ($order) {
                if ( !$order->items->isEmpty() ) {
                    foreach ( $order->items as $item ) {
                        $item->printed_qty = 0;
                        $item->save();
                    }
                }
            },
            5
        );
        $order->load(
            [
//            'place',
                'table.area',
                'customer',
                'promotions',
                'items' => function ($query) {
                    $query->where('parent_id', 0);
                },
                'items.children.product.category',
                'items.product.category',
            ]
        );
        $usingArr = [
            'place_uuid' => currentPlace()->uuid,
        ];
        return ( new PosOrderResource($order) )->using($usingArr);
    }

    /**
     * @param  \App\Http\Requests\PosOrderRequest  $request
     * @param  \App\Models\Order                   $order
     * @return \App\Http\Resources\PosOrderResource
     * @throws \Exception
     * @throws \Throwable
     */
    public function canceled(PosOrderRequest $request, Order $order)
    {
        if ( isOrderClosed($order) ) {
            throw new \Exception('ERROR: Order đã đóng không thể hủy');
        }
        DB::transaction(
            function () use ($order, $request) {
                $order->is_canceled = 1;
                $order->reason      = $request->reason;
                $order->save();
                // soft delete
                $order->delete();
            },
            5
        );
        broadcast(new OrderCanceled($order, currentPlace()))->toOthers();
        return response()->json(
            [
                'order_uuid' => $order->uuid,
                'message'    => 'OK',
            ]
        );
    }
}
