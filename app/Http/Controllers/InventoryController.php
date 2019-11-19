<?php

namespace App\Http\Controllers;
use App\Http\Resources\InventoryResource;
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

	public function show(Supply $supply) {
		dd($supply);
		$inventoryOrders = $supply->with('inventoryOrders')->paginate(request()->per_page);
		return $inventoryOrders->toJson();
		return InventoryResource::collection($inventoryOrders);
	}
}
