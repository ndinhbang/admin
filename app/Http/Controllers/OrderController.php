<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $purchases = Order::with(['supplier'])->where(function ($query) use ($request) {
                $query->where('type', $request->get('type', 0));
                // 0: Đơn xuất ( bán )
                // 1: Đơn nhập
                // 2: Đơn khách trả hàng
                // 3: Đơn trả nhà Cung cấp
                
                if($request->keyword) {
                    $query->orWhere('code', 'like', '%'.$request->keyword.'%');
                    // cần tìm theo tên sản phẩm
                }
            })
            ->orderBy('purchases.id', 'desc')
            ->paginate($request->per_page);

        return $purchases->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
