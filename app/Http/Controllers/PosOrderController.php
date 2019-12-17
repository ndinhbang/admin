<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\PosOrderRequest;
use App\Http\Resources\PosOrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Carbon\Carbon;
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
            'creator',
            'customer',
            'table',
            'items' => function ($query) {
                $query->orderBy('order_items.id', 'asc');
            },
            'items.category',
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
        $order = DB::transaction(
            function () use ($request) {
                $now   = Carbon::now();
                $order = Order::create(array_merge($request->only([ 'kind' ]), [
                    'uuid'       => nanoId(),
                    'place_id'   => currentPlace()->id,
                    'creator_id' => $request->user()->id,
                    'code'       => $request->input('code'),
                    'year'       => $now->year,
                    'month'      => $now->month,
                    'day'        => $now->day,
                ]));
                $order = $this->updateTable($request, $order);
                list($order, $products) = $this->updateItems($request, $order);
                // Customer
                $customer = getBindVal('orderCustomer');
                if ( $customer ) {
                    $order->customer_id = $customer->id;
                }
                $order->note        = $request->note ?? '';
                $order->card_name   = $request->card_name ?? '';
                $order->total_eater = $request->total_eater ?? 1;
                $order              = $this->updatePayment($request, $order);
                $order->save();
                // neu kich hoat bep
                if ( $enableKitchen = config('default.pos.enable_kitchen', false) ) {
                    // update batch
                    $this->createBatchItems($request, $order, $products);
                }
                // nếu bán thành công
                if ( $order->is_completed || $order->is_paid ) {
                    // trù kho
                    $this->subtractInventory($request, $order);
                }
                if ( $order->paid ) {
                    // tao phieu thu
                    $order->createVoucher();
                }
                return $order;
            },
            5
        );
        $order->load([
            'table',
            'customer',
            'items' => function ($query) {
                $query->orderBy('pivot_id', 'asc');
            },
        ]);
        return new PosOrderResource($order);
    }

    /**
     * @param  \App\Http\Requests\PosOrderRequest  $request
     * @param  \App\Models\Order                   $order
     * @return \App\Models\Order|mixed
     * @throws \Throwable
     */
    private function updateTable(PosOrderRequest $request, Order $order)
    {
        // todo: 1 ban co the co nhieu order
        if ( empty($request->table_uuid) ) {
            return $order;
        }
        if ( is_null($table = Table::where('uuid', $request->table_uuid)
            ->withCount([ 'orders' ])
            ->first()) ) {
            throw new \Exception('Table not found');
        }
        $order->table_id = $table->id;
        return $order;
    }

    /**
     * @param  \App\Http\Requests\PosOrderRequest  $request
     * @param  \App\Models\Order                   $order
     * @return \App\Models\Order|array
     * @throws \Exception
     */
    private function updateItems(PosOrderRequest $request, Order $order)
    {
        if ( !empty($request->items) ) {
            $collection    = ( new Collection($request->items) )
                ->unique('uuid');
            $productsUuids = $collection->pluck('uuid');
            $products      = Product::whereIn('uuid', $productsUuids)
                ->get();
            if ( $products->isEmpty() ) {
                // throw error if products not exist
                throw new \Exception('ERROR: items not found');
            }
            $keyedProducts       = $products->keyBy('uuid');
            $items               = [];
            $orderAmount         = 0;
            $discountItemsAmount = 0;
            $totalDish           = 0;
            $discountOrderAmount = $request->discount_amount ?? 0;
            $orderAmount         = $request->amount ?? 0;

            $discountOrderPercent = ($discountOrderAmount*100)/($orderAmount+$discountOrderAmount);

            foreach ( $collection as $item ) {
                $product         = $keyedProducts[ $item['uuid'] ];
                $quantity        = (int) $item['quantity'];
                $discount_amount = $item['discount_amount'] ?? 0;
                // tinh tong tien / item
                $totalPrice            = ( $quantity * $product->price ) - $discount_amount;

                // tính tiền vốn
                $totalBuyingPrice = 0;
                $totalAvgBuyingPrice = 0;
                if($product->can_stock) {
                    foreach ($product->supplies as $key => $supply) {
                        $totalBuyingPrice += $supply->pivot->quantity * $supply->price_in;
                        $totalAvgBuyingPrice += $supply->pivot->quantity * $supply->price_avg_in;
                    }
                }

                $items[ $product->id ] = [
                    'quantity'              => $quantity,
                    'total_price'           => $totalPrice,
                    'total_buying_price'     => $quantity * $totalBuyingPrice,
                    'total_buying_avg_price' => $quantity * $totalAvgBuyingPrice,
                    'discount_amount'       => $discount_amount,
                    'discount_order_amount' => round(($totalPrice*$discountOrderPercent)/100),
                    'note'                  => $item['note'] ?? '',
                    // last note on item
                    //                    'state'       => $item['state'] ?? 0,
                ];
                $discountItemsAmount   += $discount_amount;
                $totalDish += $quantity;
            }


            // cap nhat items trong order
            $changes = $order->products()
                ->sync($items);
            // cap nhat tong tien cua order
            $order->discount_items_amount = $discountItemsAmount;
            $order->discount_amount       = $discountOrderAmount;
            $order->amount                = $orderAmount;
            $order->total_dish            = $totalDish;
        }
        $order->load([ 'items' ]);
        return [
            $order,
            $keyedProducts ?? new Collection([]),
        ];
    }

    private function updatePayment(PosOrderRequest $request, Order $order)
    {
        $amount = $order->amount ?? 0;
        // Neu order chua co item
        if ( !$amount ) {
            return $order;
        }
        $paid           = 0;
        $debt           = 0;
        $isPaid         = false;
        $receivedAmount = $request->received_amount ?? 0;
        $isCompleted    = false; // hoan thanh order
        if ( $receivedAmount >= $amount ) {
            $paid        = $amount;
            $isPaid      = true;
            $isCompleted = true;
        } else {
            $paid = $receivedAmount;
            $debt = $amount - $receivedAmount;
            // Trả 1 phần cũng là đã trả, nhưng chưa hoàn thành đơn hàng
            if ( $paid ) {
                $isPaid = true;
            }
        }
        $order->paid            = $paid;
        $order->debt            = $debt;
        $order->is_paid         = $isPaid;
        $order->received_amount = $receivedAmount;
        $order->is_completed    = $isCompleted;
        return $order;
    }

    protected function createBatchItems(PosOrderRequest $request, Order $order, Collection $keyedProducts)
    {
        if ( !empty($request->items) ) {
            $newCollection = ( new Collection($request->batchItems) )
                ->unique('uuid');
            $batchs        = [];
            foreach ( $newCollection as $batchItem ) {
                $product  = $keyedProducts[ $batchItem['uuid'] ];
                $quantity = (int) $batchItem['quantity'];
                $batchs[] = [
                    'place_id'   => $order->place_id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'note'       => $batchItem['note'] ?? '',
                    'state'      => 0,
                ];
            }
            $order->batchs()
                ->createMany($batchs);
        }
    }

    /**
     * @param  \App\Http\Requests\PosOrderRequest  $request
     * @param  \App\Models\Order                   $order
     * @throws \Exception
     */
    private function subtractInventory(PosOrderRequest $request, Order $order)
    {
        $order->load([
            'items'                          => function ($query) {
                $query->where('products.can_stock', 1) // skip item khong quan ly ton kho
                ->orderBy('pivot_id', 'asc');
            },
            'items.supplies.availableStocks' => function ($query) {
                $query->where('inventory_orders.status', 1) // don nhap da hoan thanh
                ->orderBy('inventory_orders.id', 'asc');
            },
        ]);
        $items = $order->items ?? collect([]);
        if ( $items->isEmpty() ) {
            return;
        }
        foreach ( $items as $item ) {
            $supplies = $item->supplies ?? collect([]);
            if ( $supplies->isEmpty() ) {
                throw new \Exception("Chưa khai báo nguyên liệu cho sản phẩm {$item->name}.");
            };
            $now = Carbon::now()
                ->format('Y-m-d H:i:s');
            // số lượng sản phẩm trong order
            $productQuantity = $item->pivot->quantity;
            foreach ( $supplies as $supply ) {
                // dump($supply);
                $stocks = $supply->availableStocks ?? collect([]);
                // throw error if empty
                if ( $stocks->isEmpty() ) {
                    throw new \Exception("Không đủ nguyên liệu: {$supply->name} trong kho.");
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
                    throw new \Exception("{$supply->name} tồn kho không đủ số lượng");
                }
            } // end of supplies
            // unload redundant relations
            $item->unsetRelation('supplies');
        }
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
            'customer',
            'table',
            'items' => function ($query) {
                $query->orderBy('pivot_id', 'asc');
            },
            'items.category',
        ]);
        return new PosOrderResource($order);
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
        $order = DB::transaction(
            function () use ($request, $order) {
                $order = $this->updateTable($request, $order);
                list($order, $products) = $this->updateItems($request, $order);
                // Customer
                $customer = getBindVal('orderCustomer');
                if ( $customer ) {
                    $order->customer_id = $customer->id;
                }
                $order->note        = $request->note ?? '';
                $order->card_name   = $request->card_name ?? '';
                $order->total_eater = $request->total_eater ?? 1;
                $order              = $this->updatePayment($request, $order);
                $order->save();
                // neu kich hoat bep
                if ( $enableKitchen = config('default.pos.enable_kitchen', false) ) {
                    // update batch
                    $this->createBatchItems($request, $order, $products);
                }
                // nếu bán thành công
                if ( $order->is_completed || $order->is_paid ) {
                    // trù kho
                    $this->subtractInventory($request, $order);
                    // tao phieu thu
                    $order->createVoucher();
                }
                return $order;
            },
            5
        );
        $order->load([
            'table',
            'customer',
            'items' => function ($query) {
                $query->orderBy('pivot_id', 'asc');
            },
        ]);
        return new PosOrderResource($order);
    }

    /**
     * @param  \App\Http\Requests\PosOrderRequest  $request
     * @param  \App\Models\Order                   $order
     * @return \App\Http\Resources\PosOrderResource
     * @throws \Exception
     */
    public function payment(PosOrderRequest $request, Order $order)
    {
        $order = $this->updatePayment($request, $order);
        $order->save();
        $order->load([
            'table',
            'customer',
            'items' => function ($query) {
                $query->orderBy('pivot_id', 'asc');
            },
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
            'table',
            'customer',
            'items' => function ($query) {
                $query->orderBy('pivot_id', 'asc');
            },
        ]);
        return new PosOrderResource($order);
    }
}
