<?php

namespace App\Http\Controllers;

use App\Http\Filters\InventoryOrderFilter;
use App\Http\Requests\InventoryOrderRequest;
use App\Http\Resources\InventoryOrderResource;
use App\Models\InventoryOrder;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
		'supplier',
	];
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(InventoryOrderRequest $request) {

		$summary = InventoryOrder::selectRaw('SUM(amount) as total_amount, 
			SUM(debt) as total_debt, 
			COUNT(id) as total')
			->filter(new InventoryOrderFilter($request))
			->first();

		$purchases = InventoryOrder::with([
				'creator', 
				'supplier', 
				'supplies', 
				'vouchers'
			])
			->filter(new InventoryOrderFilter($request))
			->withTrashed()
			->orderBy('inventory_orders.id', 'desc')
			->paginate($request->per_page);

		return InventoryOrderResource::collection($purchases)
			->additional(['summary' => $summary]);
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

			// cập nhật giá nhập trung bình cho nguyên liệu
			foreach ($keyedArr as $supply_id => $inventory) {
				$supply = Supply::find($supply_id);
				// Lưu giá nhập trung bình trên mỗi đơn vị
				$supply->price_avg_in = $supply->avgBuyingPrice();
				$supply->save();
			}

			// tạo phiếu chi/thu tương ứng với giá nhập/trả
			if ($inventoryOrder->status) {
				// Lưu
				$voucher = $inventoryOrder->createVoucher($request->input('payment_method'), null, null, 'Thanh toán');
			
				// Cập nhật thông tin tổng quan cho account
		        $supplier->updateInventoryOrdersStats();
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
	public function destroy(InventoryOrder $inventoryOrder) {
		$deleted = DB::transaction(function () use ($inventoryOrder) {
			// thông tin nhà cung cấp
			$supplier = $inventoryOrder->supplier;

			// xóa nguyên liệu nhập của đơn nhập
			$inventoryOrder->supplies()->detach();

			// xóa phiếu chi/thu
			if ($inventoryOrder->status) {
				$vouchers = $inventoryOrder->vouchers()->delete();
			}

			$inventoryOrder->delete();

			// Cập nhật thông tin tổng quan cho account
	        $supplier->updateInventoryOrdersStats();

			return true;
		}, 5);

		return response()->json([
			'message' => $deleted ? 'Xóa đơn nhập thành công!' : 'Có lỗi xảy ra!',
		]);
	}

	/**
	 * Pay debt.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function payDebt(InventoryOrderRequest $request, $uuid) {

		if(!is_null($inventoryOrder = InventoryOrder::where('uuid', $uuid)->first())) {
			$inventoryOrder = DB::transaction(function () use ($request, $inventoryOrder) {
				$oldPaid = $inventoryOrder->paid;

				$inventoryOrder->paid = $request->input('amount');
				$note = $request->input('note');

				$voucher = $inventoryOrder->createVoucher($request->input('payment_method'), $request->user()->id, null, ($note ? $note.' | ' : '').'Trả nợ');

				$inventoryOrder->debt = $inventoryOrder->debt - $inventoryOrder->paid;
				$inventoryOrder->paid = $inventoryOrder->paid + $oldPaid;
				$inventoryOrder->save();

				// Cập nhật thông tin tổng quan cho account
		        $inventoryOrder->supplier->updateInventoryOrdersStats();

				return $inventoryOrder;
			}, 5);

			return response()->json([
				'message' => 'Trả nợ NCC thành công!',
				'data' => $inventoryOrder,
			]);
		}

		return response()->json([
			'message' => 'Có lỗi xảy ra!',
		]);
	}
}
