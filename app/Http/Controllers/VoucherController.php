<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\VoucherRequest;
use App\Models\Voucher;
use App\Models\Category;
use App\Models\Account;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $vouchers = Voucher::with(['creator', 'approver', 'category', 'payer_payee'])->orderBy('id', 'desc')->paginate($request->per_page);
        return $vouchers->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VoucherRequest $request)
    {

        $vId = Voucher::where('type', $request->type)->count();
        $vId++;
        $prefixCode = $request->type ? 'PT' : 'PC';
        $typeTxt = $request->type ? 'thu' : 'chi';

        // $request->validated();
        $voucher = new Voucher;
        $voucher->uuid = $this->nanoId();
        $voucher->code = $prefixCode.str_pad($vId, 6, "0", STR_PAD_LEFT);
        $voucher->type = $request->type; // 0:chi | 1:thu
        $voucher->imported_at = $request->imported_at;
        $voucher->amount = $request->amount;
        $voucher->payment_method = $request->payment_method;

        $category = Category::findUuid($request->category_uuid);
        if(!is_null($category))
            $voucher->category_id = $category->id;

        $account = Account::findUuid($request->payer_payee_uuid);
        if(!is_null($account))
            $voucher->payer_payee_id = $account->id;

        $voucher->title = ucwords($typeTxt).' '.$voucher->category_id;
        $voucher->note = $request->note;

        $voucher->creator_id = $request->user()->id;
        $voucher->place_id = $request->place->id;

        $voucher->save();

        return response()->json(['message' => 'Tạo phiếu '.$typeTxt.' thành công!', 'voucher' => $voucher]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        return $voucher->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Voucher $voucher)
    {
        $typeTxt = $request->type ? 'thu' : 'chi';

        $voucher->imported_at = $request->imported_at;
        $voucher->amount = $request->amount;
        $voucher->category_id = $request->category_id;
        $voucher->payment_method = $request->payment_method;

        $category = Category::findUuid($request->category_uuid);
        if(!is_null($category))
            $voucher->category_id = $category->id;

        $account = Account::findUuid($request->payer_payee_uuid);
        if(!is_null($account))
            $voucher->payer_payee_id = $account->id;

        $voucher->title = ucwords($typeTxt).' '.$voucher->category_id;
        $voucher->note = $request->note;

        $voucher->save();

        return response()->json(['message' => 'Cập nhật phiếu '.$typeTxt.' '.$voucher->code.' thành công!', 'voucher' => $voucher]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
