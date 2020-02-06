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

        $summary = Supply::selectRaw('
        	SUM(remain) as remain_total, 
        	SUM(remain*price_avg_in) as price_avg_total, 
        	SUM(remain*price_in) as price_total')
        	->where('remain', '>', 0)
        	->where('place_id', currentPlace()->id)
            ->first();

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

		$supplyInventory = Supply::select('supplies.*')
			->where(function ($query) use ($request, $stock_range) {
				if ($request->keyword) {
					$query->where('supplies.name', 'like', '%' . $request->keyword . '%');
				}

				if($request->type == 'almost') {
					$query->whereRaw('supplies.remain < supplies.min_stock');
				} else {
					$query->where('supplies.remain', '>', $stock_range[0])
						->where('supplies.remain', '<', $stock_range[1]);
				}
			})
			->with(['unit'])
			->paginate($request->per_page);

		// return $supplyInventory->toJson();
		return SupplyResource::collection($supplyInventory)
            ->additional([ 'summary' => $summary ]);
	}
	
	/**
	 * Almost out of stock.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function almostOos() {
		$supplies = Supply::select('supplies.*', DB::raw('SUM(inventory.qty_remain) as remain_total, SUM(inventory.qty_import) as quantity_total'))
			->join('inventory', 'inventory.supply_id', '=', 'supplies.id')
			->where('inventory.status', 1)
			->groupBy('supplies.id')
			->with(['unit'])
			->havingRaw('SUM(inventory.qty_remain) < supplies.min_stock')
			->get();

		return response()->json($supplies);
	}

	public function statistic(Request $request) {
		$statistic = Supply::select(DB::raw('SUM(inventory.qty_remain) as remain_total, SUM(inventory.qty_import) as quantity_total, supplies.min_stock'))
			->join('inventory', 'inventory.supply_id', '=', 'supplies.id')
			->where('inventory.status', 1)
			->groupBy('inventory.supply_id')
			->get();

		return response()->json($statistic);
	}

	public function show($supplyUuid) {
		$supply = Supply::where('supplies.uuid', $supplyUuid)->with(['unit'])->first();

		$inventory = $supply->inventory()->paginate(request()->per_page);

		return InventoryResource::collection($inventory)
            ->additional([ 'supply' => $supply ]);
	}
}
