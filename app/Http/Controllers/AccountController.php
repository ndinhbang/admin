<?php

namespace App\Http\Controllers;

use App\Http\Filters\AccountFilter;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\AccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(AccountRequest $request)
    {
        $accounts = Account::filter(new AccountFilter($request))
            ->orderBy('id', 'desc')
            ->paginate($request->per_page);
        return $accounts->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AccountRequest  $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(AccountRequest $request)
    {
        switch ( $request->type ) {
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
            default:
                throw new \Exception('Unexpected prefix code');
        };

        $account = DB::transaction(
            function () use ($request, $prefixCode) {
                //todo: refact needed
                $vId = Account::where('type', $request->type)->count();
                $vId++;

                // $request->validated();
                $account                = new Account;
                $account->uuid          = nanoId();
                $account->code          = $prefixCode . str_pad($vId, 6, "0", STR_PAD_LEFT);
                $account->type          = $request->type;
                $account->name          = $request->name;
                $account->unsigned_name = str_replace('-', ' ', \Str::slug($request->name));
                $account->contact_name  = $request->contact_name;
                $account->birth_day     = date('Y/m/d', strtotime($request->birth_day));
                if ( $request->birth_day ) {
                    $account->birth_month = date('n', strtotime($request->birth_day));
                }
                $account->gender                     = $request->gender;
                $account->address                    = $request->address;
                $account->email                      = $request->email;
                $account->phone                      = $request->phone;
                $account->tax_code                   = $request->tax_code;
                $account->note                       = $request->note;
                $account->is_corporate               = $request->is_corporate;
                $account->place_id                   = currentPlace()->id;
                $account[ 'stats->amount' ]          = 0;
                $account[ 'stats->returned_amount' ] = 0;
                $account[ 'stats->debt' ]            = 0;
                $account[ 'stats->last_order_at' ]   = null;
                $account->save();

                //todo: attach segment
                if ($account->type == 'customer') {
                    $pivotData = [];
                    $segments = Segment::cursor();
                    foreach ($segments as $segment) {
                        if ($account->isSatisfiedAllConditions($segment->conditions ?? [])) {
                            $pivotData[] = $segment->id;
                        }
                    }
                    $account->segments()->syncWithoutDetaching($pivotData);
                }
                return $account;
            },
            5
        );

        return response()->json([ 'message' => 'Tạo phiếu ' . $prefixCode . ' thành công!', 'account' => $account ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return string
     */
    public function show(Account $account)
    {
        return $account->toJson();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account       $account
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function update(Request $request, Account $account)
    {
        $account = DB::transaction(
            function () use ($request, $account) {
                $account->name          = $request->name;
                $account->unsigned_name = str_replace('-', ' ', \Str::slug($request->name));
                $account->contact_name  = $request->contact_name;
                $account->birth_day     = date('Y/m/d', strtotime($request->birth_day));
                if ( $request->birth_day ) {
                    $account->birth_month = date('n', strtotime($request->birth_day));
                }
                $account->gender       = $request->gender;
                $account->address      = $request->address;
                $account->email        = $request->email;
                $account->phone        = $request->phone;
                $account->tax_code     = $request->tax_code;
                $account->note         = $request->note;
                $account->is_corporate = $request->is_corporate;
                $account->save();

                //todo: attach segment
                if ($account->type == 'customer') {
                    $pivotData = [];
                    $segments = Segment::cursor();
                    foreach ($segments as $segment) {
                        if ($account->isSatisfiedAllConditions($segment->conditions ?? [])) {
                            $pivotData[] = $segment->id;
                        }
                    }
                    $account->segments()->syncWithoutDetaching($pivotData);
                }

                return $account;
            }, 5
        );

        return response()->json([ 'message' => 'Cập nhật ' . $account->type . ' thành công!', 'account' => $account ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }
}
