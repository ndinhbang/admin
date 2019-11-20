<?php

namespace App\Http\Controllers;
use App\Http\Resources\SupplyInventoryResource;
use App\Models\Supply;
use Illuminate\Http\Request;

class InventoryController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$supplyInventory = Supply::where(function ($query) use ($request) {
			if ($request->type) {
				$query->where('type', $request->type);
			}
		})
			->withTotalRemain()
			->paginate($request->per_page);

		// return $supplyInventory->toJson();
		return SupplyInventoryResource::collection($supplyInventory);
	}

	public function show($supplyUuid) {
		$inventoryOrders = Supply::select('inventory.*', 'inventory_orders.*', 'accounts.name as supplier_name', 'users.display_name as creator_name')
			->join('inventory', 'inventory.supply_id', '=', 'supplies.id')
			->join('inventory_orders', 'inventory_orders.id', '=', 'inventory.inventory_order_id')
			->join('accounts', 'accounts.id', '=', 'inventory_orders.supplier_id')
			->join('users', 'users.id', '=', 'inventory_orders.creator_id')
			->where('inventory_orders.status', 1)
			->where('supplies.uuid', $supplyUuid)
			->paginate(request()->per_page);

		return $inventoryOrders->toJson();
		return InventoryResource::collection($inventoryOrders);
	}
}
