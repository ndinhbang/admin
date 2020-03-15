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

        $summary = $this->statistic();

		$stock_range = [-9999999, 9999999];
		switch ($request->type) {
		case 'total': // Tất cả
			$stock_range = [-999, 9999999];
			break;
		case 'in': // Đang tồn
			$stock_range = [0, 9999999];
			break;
		case 'almost': // gần hết
			$stock_range = 'min_stock';
			break;
		case 'out': // Đã hết
			$stock_range = [-1, 1];
			break;
		case 'error': // Lỗi kho
			$stock_range = [-9999999, 0];
			break;
		}

		$supplyInventory = Supply::select('supplies.*')
			->where(function ($query) use ($request, $stock_range) {
				if ($request->keyword) {
					$query->where('supplies.name', 'like', '%' . $request->keyword . '%');
				}

				if($request->type == 'almost') {
					$query->whereRaw('supplies.remain < supplies.min_stock AND supplies.remain > 0');
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
		$supplies = Supply::select('supplies.*')
			->whereRaw('supplies.remain < supplies.min_stock AND supplies.remain > 0')
			->with(['unit'])
			->get();

		return response()->json($supplies);
	}

	public function statistic() {
        return Supply::selectRaw('
        	COUNT(*) AS total,
        	SUM(IF(remain=0, 1, 0)) AS supply_out,
        	SUM(IF(remain>0, 1, 0)) AS supply_in,
        	SUM(IF(remain<0, 1, 0)) AS supply_error,
        	SUM(case when (remain<min_stock AND remain>0) then 1 else 0 end) AS supply_almost,

        	SUM(remain) as remain_total, 
        	SUM(remain*price_avg_in) as price_avg_total, 
        	SUM(remain*price_in) as price_total')
        	->where('place_id', currentPlace()->id)
            ->first();
	}

	public function show($supplyUuid) {
		$supply = Supply::where('supplies.uuid', $supplyUuid)->with(['unit'])->first();

		$inventory = $supply->inventory()->paginate(request()->per_page);

		return InventoryResource::collection($inventory)
            ->additional([ 'supply' => $supply ]);
	}
}
