<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplyRequest;
use App\Http\Resources\SupplyResource;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyController extends Controller {
	protected $exceptAttributes = [
		'unit_uuid',
		'unit_name',
		'quantity_total',
		'remain_total',
		'updated_at',
		'created_at',
	];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
	public function index() {
		$supplies = Supply::with('unit')->get()->keyBy('uuid');

		return SupplyResource::collection($supplies);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
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
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\SupplyRequest  $request
     * @param  \App\Models\Supply                $supply
     * @return void
     * @throws \Throwable
     */
	public function update(SupplyRequest $request, Supply $supply) {
		$supply = DB::transaction(function () use ($request, $supply) {
			$category = getBindVal('__category');

			// create supply
			$supply->update(array_merge($request->except($this->exceptAttributes), [
				'unit_id' => $category->id,
			]));

			return $supply;
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
