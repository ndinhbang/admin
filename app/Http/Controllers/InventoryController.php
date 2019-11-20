<?php

namespace App\Http\Controllers;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\SupplyResource;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$supplyInventory = Supply::select('supplies.*', DB::raw('SUM(inventory.remain) as remain_total, SUM(inventory.quantity) as quantity_total'))
			->where(function ($query) use ($request) {
				if ($request->keyword) {
					$query->where('supplies.name', 'like', '%' . $request->keyword . '%');
				}
				if ($request->type == 'in') {
					$query->where('inventory.remain', '>', 0);
				}
				if ($request->type == 'long') {
					$query->where('inventory.remain', '=', 0);
				}
				if ($request->type == 'out') {
					$query->where('inventory.remain', '=', 0);
				}
				if ($request->type == 'nearly') {
					$query->whereBetween('inventory.remain', [1, 9]);
				}
				if ($request->type == 'error') {
					$query->where('inventory.remain', '<', 0);
				}
			})
			->join('inventory', 'inventory.supply_id', '=', 'supplies.id')
			->groupBy('supplies.id')
			->with(['unit'])
			->paginate($request->per_page);

		// return $supplyInventory->toJson();
		return SupplyResource::collection($supplyInventory);
	}

	public function show($supplyUuid) {
		$inventoryOrders = Supply::select(
			'inventory.*',
			'inventory_orders.*',
			'accounts.name as supplier_name',
			'users.display_name as creator_name')
			->join('inventory', 'inventory.supply_id', '=', 'supplies.id')
			->join('inventory_orders', 'inventory_orders.id', '=', 'inventory.inventory_order_id')
			->join('accounts', 'accounts.id', '=', 'inventory_orders.supplier_id')
			->join('users', 'users.id', '=', 'inventory_orders.creator_id')
			->where('inventory_orders.status', 1)
			->where('supplies.uuid', $supplyUuid)
			->orderBy('inventory_orders.created_at', 'desc')
			->paginate(request()->per_page);

		return InventoryResource::collection($inventoryOrders);
	}
}
