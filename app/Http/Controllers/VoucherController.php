<?php

namespace App\Http\Controllers;

use App\Http\Filters\VoucherFilter;
use App\Http\Requests\VoucherRequest;
use App\Http\Resources\VoucherResource;
use App\Models\Order;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    protected $exceptAttributes = [
        'payer_payee_uuid',
        'payer_payee_name',
        'payer_payee_code',
        'payer_payee_type',
        'category_uuid',
        'category_name',
        'creator_uuid',
        'creator_name',
        'updated_at',
        'created_at',
        'place',
    ];

    public function overview(VoucherRequest $request)
    {
        $overview = Voucher::select(
            DB::raw(
                "
                SUM(if(vouchers.type='0',amount,0)) as chi_amount,
                SUM(if(vouchers.type='1',amount,0)) as thu_amount,
                SUM(if(vouchers.payment_method='cash',amount,0)) as tienmat_amount,
                SUM(if((vouchers.payment_method='transfer' OR vouchers.payment_method='bank_card'),amount,0)) as taikhoan_amount,
                SUM(if(vouchers.type='0' AND vouchers.payment_method='cash',amount,0)) as chi_tienmat,
                SUM(if(vouchers.type='0' AND (vouchers.payment_method='transfer' OR vouchers.payment_method='bank_card'),amount,0)) as chi_taikhoan,
                SUM(if(vouchers.type='1' AND vouchers.payment_method='cash',amount,0)) as thu_tienmat,
                SUM(if(vouchers.type='1' AND (vouchers.payment_method='transfer' OR vouchers.payment_method='bank_card'),amount,0)) as thu_taikhoan
            "
            )
        )
            ->filter(new VoucherFilter($request))
            ->orderBy('vouchers.id', 'desc')
            ->first();
        // Tổng chi thu theo từng danh mục
        $byCategories = Voucher::select(
            DB::raw("SUM(amount) as amount_total, vouchers.*, categories.uuid, categories.name")
        )
            ->filter(new VoucherFilter($request))
            ->join('categories', 'categories.id', '=', 'vouchers.category_id')
            ->groupBy('vouchers.category_id')
            ->orderBy('categories.id', 'asc')
            ->get();
        return response()->json(compact([ 'overview', 'byCategories' ]));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Http\Requests\VoucherRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(VoucherRequest $request)
    {
        $category = getBindVal('category');

        $vouchers = Voucher::with([ 'creator', 'approver', 'category', 'payer_payee' ])
            ->filter(new VoucherFilter($request))
            ->where(function ($query) use ($request, $category) {
                if (!is_null($category)) {
                    $query->where('vouchers.category_id', $category->id);
                }
            })
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 10);
        return VoucherResource::collection($vouchers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\VoucherRequest  $request
     * @return void
     * @throws \Throwable
     */
    public function store(VoucherRequest $request)
    {
        $voucher = DB::transaction(
            function () use ($request) {
                $placeId     = currentPlace()->id;
                $category    = getBindVal('category');
                $payer_payee = getBindVal('account');
                // create voucher
                $voucher = Voucher::create(
                    array_merge(
                        $request->except($this->exceptAttributes),
                        [
                            'uuid'           => nanoId(),
                            'payer_payee_id' => $payer_payee->id,
                            'category_id'    => $category->id,
                            'creator_id'     => $request->user()->id,
                            'place_id'       => $placeId,
                            'code'           => $request->input('code'),
                        ]
                    )
                );
                return $voucher;
            },
            5
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \App\Http\Resources\VoucherResource
     */
    public function show(Voucher $voucher)
    {
        return new VoucherResource($voucher->load([ 'creator', 'category', 'payer_payee', 'approver' ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\VoucherRequest  $request
     * @param  \App\Models\Voucher                $voucher
     * @return void
     * @throws \Throwable
     */
    public function update(VoucherRequest $request, Voucher $voucher)
    {
        $voucher = DB::transaction(
            function () use ($request, $voucher) {
                $category    = getBindVal('category');
                $payer_payee = getBindVal('account');
                // update voucher
                $voucher->guard([ 'id', 'uuid', 'place_id', 'code' ]);
                $voucher->update(
                    array_merge(
                        $request->except($this->exceptAttributes),
                        [
                            'payer_payee_id' => $payer_payee->id,
                            'category_id'    => $category->id,
                        ]
                    )
                );
                return $voucher;
            },
            5
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\VoucherRequest  $request
     * @param  \App\Models\Voucher                $voucher
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function destroy(VoucherRequest $request, Voucher $voucher)
    {
        DB::transaction(
            function () use ($voucher, $request) {
                $reason = "{$voucher->code} bị xóa bởi {$request->user()->name}";
                // todo: hủy đơn hàng ứng với phiếu thu bán hàng
                if ($voucher->category_id === 29) {
                    $order = Order::find($voucher->order_id);
                    $order->is_returned = 0;
                    $order->is_canceled = 1;
                    $order->is_served = 0;
                    $order->is_paid = 0;
                    $order->is_completed = 0;
                    $order->reason = $reason;
                    $order->save();
                    $order->delete();
                }
                $voucher->note = $voucher->note . "(xóa bởi {$request->user()->name})";
                $voucher->save();
                // xóa
                $voucher->delete();
            },
            5
        );

        return response()->json([ 'message' => 'Xóa phiếu thành công' ]);
    }
}
