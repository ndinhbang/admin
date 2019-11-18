<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryOrderRequest;
use App\Http\Resources\InventoryOrderResource;
use App\Models\InventoryOrder;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryOrderController extends Controller {
	protected $exceptAttributes = [
		'supplies',
		'supplier_uuid',
		'supplier_name',
		'supplier_code',
		'supplier_type',
		'creator_uuid',
		'creator_name',
		'updated_at',
		'created_at',
		'payment_method',
		'place',
	];
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$purchases = InventoryOrder::with(['creator', 'supplier', 'supplies'])->where(function ($query) use ($request) {
			$query->where('type', $request->get('type', 1));
			// 0: Đơn trả nhà Cung cấp
			// 1: Đơn nhập

			if ($request->keyword) {
				$query->orWhere('code', 'like', '%' . $request->keyword . '%');
				// cần tìm theo tên sản phẩm
			}
		})
			->orderBy('inventory_orders.id', 'desc')
			->paginate($request->per_page);

		return InventoryOrderResource::collection($purchases);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(InventoryOrderRequest $request) {
		$inventoryOrder = DB::transaction(function () use ($request) {
			$placeId = currentPlace()->id;

			$supplier = getBindVal('account');

			// create inventory order
			$inventoryOrder = InventoryOrder::create(array_merge($request->except($this->exceptAttributes), [
				'uuid' => nanoId(),
				'supplier_id' => $supplier->id,
				'creator_id' => $request->user()->id,
				'user_id' => $request->user()->id,
				'place_id' => $placeId,
				'code' => $request->input('code'),
			]));

			// add supplies
			$keyedArr = $this->addSupplies($inventoryOrder, $request->input('supplies', []));
			$inventoryOrder->supplies()->attach($keyedArr);

			// tạo phiếu chi/thu tương ứng với giá nhập/trả
			if ($inventoryOrder->status) {
				// Lưu
				$voucher = $inventoryOrder->createVoucher($request->input('payment_method'));
			}

			return $inventoryOrder;
		}, 5);
	}

	/**
	 * Create supplies and then return array that is ready for attach to pivot table
	 *
	 * @param InventoryOrder $inventoryOrder
	 * @param array   $arrSupplies
	 * @return array
	 */
	protected function addSupplies(InventoryOrder $inventoryOrder, array $arrSupplies) {
		$result = [];
		$collection = new Collection($arrSupplies);

		foreach ($collection as $item) {
			$supply = Supply::firstOrNew([
				'place_id' => $inventoryOrder->place_id,
				'name' => $item['name'],
			]);

			if (!$supply->id) {
				// generate uuid
				$supply->uuid = $supply->uuid ?? nanoId();
				$supply->save();
			}

			$result[$supply->id] = [
				'quantity' => $item['quantity'],
				'remain' => $item['quantity'],
				'total_price' => $item['total_price'],
				'price_pu' => round($item['total_price'] / $item['quantity']),
			];

			// Lưu giá nhập trên mỗi đơn vị mới nhất
			$supply->price_in = $result[$supply->id]['price_pu'];
			$supply->save();
		}
		return $result;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(InventoryOrder $inventoryOrder) {
		return new InventoryOrderResource($inventoryOrder->load(['creator', 'supplier', 'supplies']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(InventoryOrderRequest $request, InventoryOrder $inventoryOrder) {
		$inventoryOrder = DB::transaction(function () use ($request, $inventoryOrder) {

			$inventoryOrder->createVoucher();

			$supplier = getBindVal('account');

			// update inventory order
			$inventoryOrder->guard(['id', 'uuid', 'place_id', 'code']);
			$inventoryOrder->update(array_merge($request->except($this->exceptAttributes), [
				'supplier_id' => $supplier->id,
			]));

			// sync supplies
			$keyedArr = $this->addSupplies($inventoryOrder, $request->input('supplies', []));
			$inventoryOrder->supplies()->sync($keyedArr);

			// tạo phiếu chi/thu tương ứng với giá nhập/trả

			return $inventoryOrder;
		}, 5);
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
