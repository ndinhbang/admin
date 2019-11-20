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
     * @param PosOrderRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(PosOrderRequest $request)
    {
        $orders = Order::with(['creator', 'customer', 'items', 'table'])->filter(new OrderFilter($request))
            ->orderBy('orders.id', 'desc')->paginate(6);

        return PosOrderResource::collection($orders);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param PosOrderRequest $request
     * @return PosOrderResource
     * @throws \Exception
     */
    public function store(PosOrderRequest $request)
    {
        $now = Carbon::now();

        $order = Order::create(array_merge($request->only(['kind']), [
            'uuid'       => nanoId(),
            'place_id'   => currentPlace()->id,
            'creator_id' => $request->user()->id,
            'code'       => $request->input('code'),
            'year'       => $now->year,
            'month'      => $now->month,
            'day'        => $now->day,
        ]));

        $order->load('items');

        return new PosOrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return PosOrderResource
     */
    public function show(Order $order)
    {
        $order->load([
            'items' => function ($query) {
                $query->orderBy('pivot_id', 'asc');
            },
        ]);

        return new PosOrderResource($order);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param PosOrderRequest $request
     * @param Order           $order
     * @return PosOrderResource
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(PosOrderRequest $request, Order $order)
    {
        DB::transaction(function () use ($request, $order) {
            $place = currentPlace();
            $items = $request->input('items', []);
            $table = $request->input('table', []);


            if (!is_null($table = Table::where('uuid', $table['uuid'] ?? '')->first())) {
                $table->order_id = $order->id;
                $table->state = 1;
                $table->save();
            }

            if (!empty($items)) {
                $collection = (new Collection($items))->unique('uuid');
                // query products
                $products = Product::whereIn('uuid', $collection->pluck('uuid'))->get()->keyBy('uuid');
                if ($products->isEmpty()) {
                    throw new \Exception('Items not found');
                }

                $datas = [];
                $orderAmount = 0;
                $totalDish = 0;
                foreach ($collection as $item) {
                    if (!$products->has($item['uuid'])) {
                        continue;
                    }
                    $product = $products[$item['uuid']];
                    $quantity = (int)$item['quantity'];
                    // calculate total cost and discount amount
                    if ($product->price_sale > 0) {
                        $totalPrice = $quantity * $product->price_sale;
                    } else {
                        $totalPrice = $quantity * $product->price;
                    }

                    $orderAmount += $totalPrice;
                    $totalDish++;
                    
                    $datas[$product->id] = [
                        'quantity'    => $quantity,
                        'total_price' => $totalPrice,
                        'note' => $item['note'] ?? '',
                    ];
                }
                // cap nhat items trong order
                $order->products()->sync($datas);
                // cap nhat tong tien cua order
                $order->amount = $orderAmount;
                $order->total_dish = $totalDish;
                $order->save();
            }
        }, 5);


        $order->load([
            'table',
            'items' => function ($query) {
                $query->orderBy('pivot_id', 'asc');
            },
        ]);

        return new PosOrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function addItem(PosOrderRequest $request, Order $order, Product $product)
    {
        if ($product->price_sale > 0) {
            $totalPrice = 1 * $product->price_sale;
        } else {
            $totalPrice = 1 * $product->price;
        }
        $order->items()->attach($product->id, [
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'OK',
        ]);
    }

    public function updateItem(PosOrderRequest $request, Order $order, Product $product)
    {
        $quantity = (int)$request->input('quantity');
        if ($product->price_sale > 0) {
            $totalPrice = $quantity * $product->price_sale;
        } else {
            $totalPrice = $quantity * $product->price;
        }
        $order->items()->updateExistingPivot($product->id, [
            'quantity'    => $quantity,
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'OK',
        ]);
    }
}
