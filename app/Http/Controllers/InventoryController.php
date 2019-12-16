<?php

namespace App\Http\Controllers;
use App\Http\Resources\InventoryResource;
use App\Http\Resources\SupplyResource;
use App\Models\Inventory;
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

		$stock_range = [-999, 9999999];
		switch ($request->type) {
		case 'all': // Tất cả
			$stock_range = [-999, 9999999];
			break;
		case 'in': // Đang tồn
			$stock_range = [0, 9999999];
			break;
		case 'almost': // Đang tồn
			$stock_range = 'min_stock';
			break;
		case 'out': // Đã hết
			$stock_range = [-1, 1];
			break;
		case 'error': // Lỗi kho
			$stock_range = [-999, 0];
			break;
		}

		$supplyInventory = Supply::select('supplies.*', DB::raw('SUM(inventory.remain) as remain_total, SUM(inventory.quantity) as quantity_total'))
			->where(function ($query) use ($request, $stock_range) {
				if ($request->keyword) {
					$query->where('supplies.name', 'like', '%' . $request->keyword . '%');
				}
			})
			->join('inventory', 'inventory.supply_id', '=', 'supplies.id')
			->groupBy('supplies.id')
			->with(['unit']);

		if($request->type == 'almost') {
			$supplyInventory = $supplyInventory->havingRaw('SUM(inventory.remain) < supplies.min_stock')
				->get();
		} else {
			$supplyInventory = $supplyInventory->havingRaw('SUM(inventory.remain) > ? AND SUM(inventory.remain) < ?', $stock_range)
				->paginate($request->per_page);
		}

		// return $supplyInventory->toJson();
		return SupplyResource::collection($supplyInventory);
	}
	
	/**
	 * Almost out of stock.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function almostOos() {
		$supplies = Supply::select('supplies.*', DB::raw('SUM(inventory.remain) as remain_total, SUM(inventory.quantity) as quantity_total'))
			->join('inventory', 'inventory.supply_id', '=', 'supplies.id')
			->groupBy('supplies.id')
			->with(['unit'])
			->havingRaw('SUM(inventory.remain) < supplies.min_stock')
			->get();

		return response()->json($supplies);
	}

	public function statistic(Request $request) {
		$statistic = Inventory::select(DB::raw('SUM(inventory.remain) as remain_total, SUM(inventory.quantity) as quantity_total, supplies.min_stock'))
			->join('supplies', 'inventory.supply_id', '=', 'supplies.id')
			->groupBy('inventory.supply_id')
			->get();

		return response()->json($statistic);
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
