<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use App\Models\Account;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $account = Account::where(function ($query) use ($request) {
                if($request->type) {
                    $query->where('type', $request->type);
                }
                if($request->keyword) {
                    $query->orWhere('code', 'like', '%'.$request->keyword.'%');
                    $query->orWhere('unsigned_name', 'like', '%'.$request->keyword.'%');
                    $query->orWhere('phone', 'like', '%'.$request->keyword.'%');
                }
            })->orderBy('id', 'desc')->paginate(20);
        return $account->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request)
    {
        $vId = Account::where('type', $request->type)->count();
        $vId++;

        switch ($request->type) {
            case 'customer':
                $prefixCode = 'ACU';
                break;
            case 'employee':
                $prefixCode = 'AEM';
                break;
            case 'supplier':
                $prefixCode = 'ASU';
                break;
            case 'shipper':
                $prefixCode = 'ASH';
                break;
        };

        // $request->validated();
        $account = new Account;
        $account->uuid = $this->nanoId();
        $account->code = $prefixCode.str_pad($vId, 6, "0", STR_PAD_LEFT);
        $account->type = $request->type; // 0:chi | 1:thu
        $account->name = $request->name;
        $account->unsigned_name = str_replace('-', ' ',\Str::slug($request->name));
        $account->contact_name = $request->contact_name;
        $account->birth_day = date('Y/m/d', strtotime($request->birth_day));
        if($request->birth_day)
            $account->birth_month = date('n', strtotime($request->birth_day));

        $account->gender = $request->gender;
        $account->address = $request->address;
        $account->email = $request->email;
        $account->phone = $request->phone;
        $account->tax_code = $request->tax_code;
        $account->note = $request->note;

        $account->place_id = $request->place->id;

        $account->save();

        return response()->json(['message' => 'Tạo phiếu '.$prefixCode.' thành công!', 'account' => $account]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return $account->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $account->name = $request->name;
        $account->unsigned_name = str_replace('-', ' ',\Str::slug($request->name));
        $account->contact_name = $request->contact_name;
        $account->birth_day = date('Y/m/d', strtotime($request->birth_day));
        if($request->birth_day)
            $account->birth_month = date('n', strtotime($request->birth_day));

        $account->gender = $request->gender;
        $account->address = $request->address;
        $account->email = $request->email;
        $account->phone = $request->phone;
        $account->tax_code = $request->tax_code;
        $account->note = $request->note;

        $account->save();

        return response()->json(['message' => 'Cập nhật '.$account->type.' thành công!', 'account' => $account]);
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
