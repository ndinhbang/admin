<?php

namespace App\Http\Controllers;

use App\Http\Filters\InventoryTakeFilter;
use App\Http\Requests\InventoryTakeRequest;
use App\Http\Resources\InventoryTakeResource;
use App\Models\Inventory;
use App\Models\InventoryTake;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryTakeController extends Controller {
    protected $exceptAttributes = [
        'supplies',
        'creator_uuid',
        'creator_name',
        'updated_at',
        'created_at',
        'place',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InventoryTakeRequest $request) {

        $inventoryTakes = InventoryTake::with([
                'creator', 
                'supplies'
            ])
            ->filter(new InventoryTakeFilter($request))
            ->orderBy('inventory_takes.id', 'desc')
            ->paginate($request->per_page);

        return InventoryTakeResource::collection($inventoryTakes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InventoryTakeRequest $request) {
        $inventoryTake = DB::transaction(function () use ($request) {
            $placeId = currentPlace()->id;

            // create inventory order
            $inventoryTake = InventoryTake::create(array_merge($request->except($this->exceptAttributes), [
                'uuid' => nanoId(),
                'creator_id' => $request->user()->id,
                'user_id' => $request->user()->id,
                'place_id' => $placeId,
                'code' => $request->input('code'),
            ]));

            // add supplies
            $keyedArr = $this->addSupplies($inventoryTake, $request->input('supplies', []));
            $inventoryTake->supplies()->attach($keyedArr['supplies']);
            
            // 
            $inventoryTake->qty = $keyedArr['stats']['total'];
            $inventoryTake->qty_diff = $keyedArr['stats']['diff'];
            $inventoryTake->qty_excessing = $keyedArr['stats']['excessing'];
            $inventoryTake->qty_missing = $keyedArr['stats']['missing'];
            
            $inventoryTake->save();
            
            return $inventoryTake;
        }, 5);

        return new InventoryTakeResource($inventoryTake->load(['creator','supplies']));;
    }

    /**
     * Create supplies and then return array that is ready for attach to pivot table
     *
     * @param InventoryTake $inventoryTake
     * @param array   $arrSupplies
     * @return array
     */
    protected function addSupplies(InventoryTake $inventoryTake, array $arrSupplies) {
        $result = [];
        $total = $diff = $missing = $excessing = 0;

        $collection = new Collection($arrSupplies);

        foreach ($collection as $key => $item) {
            $supply = Supply::findUuid($item['uuid']);

            if(is_null($supply)) {
                throw new \Exception($item['name']." không tồn tại!");
            }

            // lấy dữ liệu kho theo nguyên liệu gần nhất
            $lastInventory = $supply->inventory()->first();

            $qtyRemain = is_null($lastInventory) ? $item['qty_diff'] : $lastInventory->qty_remain - $item['qty_diff'];

            $qtyDiff = abs($item['qty_diff']);

            $result['supplies'][$supply->id] = [
                'ref_code' => $inventoryTake->code,
                'qty_import' => $item['qty_diff'] < 0 ? $qtyDiff : 0,
                'qty_export' => $item['qty_diff'] > 0 ? $qtyDiff : 0,
                'qty_remain' => $qtyRemain,
                'total_price' => round($supply->price_in * $qtyDiff),
                'price_pu' => $supply->price_in,
                'status' => $inventoryTake->status,
                'note' => isset($item['note']) ? $item['note'] : ''
            ];

            $total++;

            if($item['qty_diff'] != 0)
                $diff++;

            if($item['qty_diff'] > 0)
                $excessing++;

            if($item['qty_diff'] < 0)
                $missing++;

            $result['stats'] = [
                'total' => $total,
                'diff' => $diff,
                'missing' => $missing,
                'excessing' => $excessing,
            ];

            // cập nhật giá nhập trung bình cho nguyên liệu
            if($inventoryTake->status) {
                $supply->remain = $qtyRemain;
                $supply->save();
            }
        }
        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(InventoryTake $inventoryTake) {
        return new InventoryTakeResource($inventoryTake->load(['creator','supplies']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\InventoryTakeRequest  $request
     * @param  \App\Models\InventoryTake                $inventoryTake
     * @return \App\Http\Resources\InventoryTakeResource
     * @throws \Throwable
     */
    public function update(InventoryTakeRequest $request, InventoryTake $inventoryTake) {
        $inventoryTake = DB::transaction(function () use ($request, $inventoryTake) {

            $supplier = getBindVal('__account');

            // update inventory order
            $inventoryTake->guard(['id', 'uuid', 'place_id', 'code']);
            $inventoryTake->update(array_merge($request->except($this->exceptAttributes), [
                'creator_id' => $request->user()->id,
            ]));

            // sync supplies
            $keyedArr = $this->addSupplies($inventoryTake, $request->input('supplies', []));

            $inventoryTake->supplies()->sync($keyedArr['supplies']);

            // 
            $inventoryTake->qty = $keyedArr['stats']['total'];
            $inventoryTake->qty_diff = $keyedArr['stats']['diff'];
            $inventoryTake->qty_excessing = $keyedArr['stats']['excessing'];
            $inventoryTake->qty_missing = $keyedArr['stats']['missing'];
            
            $inventoryTake->save();

            return $inventoryTake;
        }, 5);

        return new InventoryTakeResource($inventoryTake->load(['creator','supplies']));;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryTake $inventoryTake) {
        $deleted = DB::transaction(function () use ($inventoryTake) {
            // thông tin nhà cung cấp
            $supplier = $inventoryTake->supplier;

            // xóa nguyên liệu nhập của đơn nhập
            // $inventoryTake->supplies()->detach();

            // xóa phiếu chi/thu
            if ($inventoryTake->status) {
                $vouchers = $inventoryTake->vouchers()->delete();
            }

            $inventoryTake->delete();

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
    public function execBalance(InventoryTakeRequest $request, $uuid) {

        if(!is_null($inventoryTake = InventoryTake::where('uuid', $uuid)->first())) {
            $inventoryTake = DB::transaction(function () use ($request, $inventoryTake) {
                $oldPaid = $inventoryTake->paid;

                $inventoryTake->paid = $request->input('amount');
                $note = $request->input('note');

                $voucher = $inventoryTake->createVoucher($request->input('payment_method'), $request->user()->id, null, ($note ? $note.' | ' : '').'Trả nợ');

                $inventoryTake->debt = $inventoryTake->debt - $inventoryTake->paid;
                $inventoryTake->paid = $inventoryTake->paid + $oldPaid;
                $inventoryTake->save();

                // Cập nhật thông tin tổng quan cho account
                $inventoryTake->supplier->updateInventoryOrdersStats();

                return $inventoryTake;
            }, 5);

            return response()->json([
                'message' => 'Trả nợ NCC thành công!',
                'data' => $inventoryTake,
            ]);
        }

        return response()->json([
            'message' => 'Có lỗi xảy ra!',
        ]);
    }
}
