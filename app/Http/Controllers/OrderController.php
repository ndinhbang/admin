<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param    \App\Http\Requests\OrderRequest    $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index( OrderRequest $request )
    {
        $summary = Order::selectRaw('
                COUNT(orders.id) as total_order,
                SUM(if(orders.discount_amount > 0,1,0)) as total_discount,
                SUM(if(orders.discount_items_amount > 0,1,0)) as total_discount_items,
                SUM(if(orders.debt > 0,1,0)) as total_debt,
                SUM(orders.amount) as total_amount,
                SUM(orders.discount_amount) as total_discount_amount,
                SUM(orders.discount_items_amount) as total_discount_items_amount,
                SUM(orders.debt) as total_debt_amount')
            ->where('orders.is_paid', true)
            ->filter(new OrderFilter($request))
            ->first();

        $orders  = Order::with([
            'creator',
            'customer',
            'table',
            'table.area',
            'items' => function ( $query ) {
                // $query->where('parent_id', 0);
            },
            'items.children',
            'items.product.category',
        ])
            ->filter(new OrderFilter($request))
            ->orderBy('orders.id', 'desc')
            ->withTrashed()
            ->paginate($request->per_page);
        return OrderResource::collection($orders)
            ->additional([ 'summary' => $summary ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param    \Illuminate\Http\Request    $request
     * @return \Illuminate\Http\Response
     */
    public function store( OrderRequest $request )
    {
        //
    }

    /**
     * Display the specified resource.
     * @param    int    $id
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param    \Illuminate\Http\Request    $request
     * @param    int                         $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, $id )
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param    int    $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Order $order )
    {
        $deleted = DB::transaction(function () use ($order) {
            // thông tin nhà cung cấp
            $customer = $order->customer;

            // xóa phiếu chi/thu
            if ($order->is_paid) {
                $vouchers = $order->vouchers()->delete();
            }

            $order->is_canceled = 1;
            $order->is_paid = 0;
            $order->is_completed = 0;
            $order->save();
            
            $order->delete();

            // Cập nhật thông tin tổng quan cho account
            if(!is_null($customer))
                $customer->updateOrdersStats();

            return true;
        }, 5);

        return response()->json([
            'message' => $deleted ? 'Xóa đơn hàng thành công!' : 'Có lỗi xảy ra!',
        ]);
    }
}
