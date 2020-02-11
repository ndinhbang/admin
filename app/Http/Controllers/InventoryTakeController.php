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
            ->orderBy('inventory_orders.id', 'desc')
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
            $inventoryTake->supplies()->attach($keyedArr);

            // tạo phiếu chi/thu tương ứng với giá nhập/trả
            if ($inventoryTake->status) {

                // cập nhật giá nhập trung bình cho nguyên liệu
                foreach ($keyedArr as $supply_id => $inventory) {
                    $supply = Supply::find($supply_id);
                    $supply->price_avg_in = $supply->avgBuyingPrice();
                    $supply->save();
                }
                
                // Lưu
                $voucher = $inventoryTake->createVoucher($request->input('payment_method'), null, null, 'Thanh toán');
            
                // Cập nhật thông tin tổng quan cho account
                $supplier->updateInventoryOrdersStats();
            }

            return $inventoryTake;
        }, 5);
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
        $collection = new Collection($arrSupplies);

        foreach ($collection as $item) {
            $supply = Supply::findUuid($item['uuid']);

            if(is_null($supply)) {
                throw new \Exception($item['name']." không tồn tại!");
            }

            // lấy dữ liệu kho theo nguyên liệu gần nhất
            $lastInventory = $supply->inventory()->first();

            $qtyRemain = is_null($lastInventory) ? $item['qty_diff'] : $lastInventory->qty_remain + $item['qty_diff'];

            $qtyDiff = abs($item['qty_diff']);

            $result[$supply->id] = [
                'ref_code' => $inventoryTake->code,
                'qty_import' => $item['qty_diff'] < 0 ? $qtyDiff : 0,
                'qty_export' => $item['qty_diff'] > 0 ? $qtyDiff : 0,
                'qty_remain' => $qtyRemain,
                'total_price' => round($lastInventory->price_pu * $qtyDiff),
                'price_pu' => $lastInventory->price_pu,
                'status' => $inventoryTake->status,
                'note' => $item['note']
            ];

            // cập nhật giá nhập trung bình cho nguyên liệu
            if($inventoryTake->status) {
                $supply->remain = $qtyRemain;
            }
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
    public function show(InventoryTake $inventoryTake) {
        return new InventoryTakeResource($inventoryTake->load(['creator', 'supplier', 'supplies']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InventoryTakeRequest $request, InventoryTake $inventoryTake) {
        $inventoryTake = DB::transaction(function () use ($request, $inventoryTake) {

            $inventoryTake->createVoucher();

            $supplier = getBindVal('account');

            // update inventory order
            $inventoryTake->guard(['id', 'uuid', 'place_id', 'code']);
            $inventoryTake->update(array_merge($request->except($this->exceptAttributes), [
                'supplier_id' => $supplier->id,
            ]));

            // sync supplies
            $keyedArr = $this->addSupplies($inventoryTake, $request->input('supplies', []));
            $inventoryTake->supplies()->sync($keyedArr);

            // tạo phiếu chi/thu tương ứng với giá nhập/trả

            return $inventoryTake;
        }, 5);
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
