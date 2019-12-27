<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
	 */
	public function index(OrderRequest $request) {
		$summary = Order::selectRaw('
                COUNT(orders.id) as total_order,
                SUM(if(orders.discount_amount > 0,1,0)) as total_discount,
                SUM(if(orders.discount_items_amount > 0,1,0)) as total_discount_items,
                SUM(if(orders.debt > 0,1,0)) as total_debt,
                SUM(orders.amount) as total_amount,
                SUM(orders.discount_amount) as total_discount_amount,
                SUM(orders.discount_items_amount) as total_discount_items_amount,
                SUM(orders.debt) as total_debt_amount')
			->filter(new OrderFilter($request))
			->first();

		$orders = Order::with([
			'creator',
			'customer',
			'table',
            'items',
            'items.category',
		])
			->filter(new OrderFilter($request))
			->orderBy('orders.id', 'desc')
            ->paginate($request->per_page);
            
		return OrderResource::collection($orders)
			->additional(['summary' => $summary]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(OrderRequest $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
