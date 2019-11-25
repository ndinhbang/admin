<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoucherRequest;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller {
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

	public function overview(Request $request) {

		$vouchers = Voucher::select(DB::raw("
                SUM(if(vouchers.type='0',amount,0)) as chi_amount,
                SUM(if(vouchers.type='1',amount,0)) as thu_amount,
                SUM(if(vouchers.type='0' AND vouchers.payment_method='cash',amount,0)) as chi_tienmat,
                SUM(if(vouchers.type='0' AND (vouchers.payment_method='transfer' OR vouchers.payment_method='bank_card'),amount,0)) as chi_taikhoan,
                SUM(if(vouchers.type='1' AND vouchers.payment_method='cash',amount,0)) as thu_tienmat,
                SUM(if(vouchers.type='1' AND (vouchers.payment_method='transfer' OR vouchers.payment_method='bank_card'),amount,0)) as thu_taikhoan,
                SUM(if(vouchers.category_id='21',amount,0)) as chimuahang_amount,
                SUM(if(vouchers.category_id='22',amount,0)) as tientrahang_amount,
                SUM(if(vouchers.category_id='23',amount,0)) as chidauky_amount,
                SUM(if(vouchers.category_id='24',amount,0)) as chitamung_amount,
                SUM(if(vouchers.category_id='25',amount,0)) as chihoanung_amount,
                SUM(if(vouchers.category_id='26',amount,0)) as chirutvon_amount,
                SUM(if(vouchers.category_id='27',amount,0)) as cuocphi_amount,
                SUM(if(vouchers.category_id='28',amount,0)) as chikhac_amount,

                SUM(if(vouchers.category_id='29',amount,0)) as thubanhang_amount,
                SUM(if(vouchers.category_id='30',amount,0)) as thuxuattra_amount,
                SUM(if(vouchers.category_id='31',amount,0)) as thugopvon_amount,
                SUM(if(vouchers.category_id='32',amount,0)) as thutamung_amount,
                SUM(if(vouchers.category_id='33',amount,0)) as thuhoaung_amount,
                SUM(if(vouchers.category_id='34',amount,0)) as thunocod_amount,
                SUM(if(vouchers.category_id='35',amount,0)) as thukhac_amount
            "))
			->where(function ($query) use ($request) {
				// date time range
				$startDate = Carbon::parse($request->get('start', Carbon::now()))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse($request->get('end', Carbon::now()))->format('Y-m-d 23:59:59');

				$query->whereBetween('created_at', [$startDate, $endDate]);
			})
			->orderBy('vouchers.id', 'desc')
			->first();
		return response()->json(compact('vouchers'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {

		$vouchers = Voucher::where(function ($query) use ($request) {
			if ($request->keyword) {
				$query->orWhere('code', 'like', '%' . $request->keyword . '%');
			}
			// by type; 0:chi | 1: thu
			if ($request->type) {
				$query->where('type', $request->type);
			}
			// date time range
			if (!is_null($request->get('start', null)) && !is_null($request->get('end', null))) {

				$startDate = Carbon::parse($request->get('start', null))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse($request->get('end', null))->format('Y-m-d 23:59:59');

				$query->whereBetween('created_at', [$startDate, $endDate]);
			}
		})
			->with(['creator', 'approver', 'category', 'payer_payee'])
			->orderBy('id', 'desc')
			->paginate($request->per_page);

		return VoucherResource::collection($vouchers);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(VoucherRequest $request) {

		$voucher = DB::transaction(function () use ($request) {
			$placeId = currentPlace()->id;

			$category = getBindVal('category');
			$payer_payee = getBindVal('account');

			// create voucher
			$voucher = Voucher::create(array_merge($request->except($this->exceptAttributes), [
				'uuid' => nanoId(),
				'payer_payee_id' => $payer_payee->id,
				'category_id' => $category->id,
				'creator_id' => $request->user()->id,
				'place_id' => $placeId,
				'code' => $request->input('code'),
			]));

			return $voucher;
		}, 5);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Voucher $voucher) {
		return new VoucherResource($voucher->load(['creator', 'category', 'payer_payee', 'approver']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(VoucherRequest $request, Voucher $voucher) {

		$voucher = DB::transaction(function () use ($request, $voucher) {

			$category = getBindVal('category');
			$payer_payee = getBindVal('account');

			// update voucher
			$voucher->guard(['id', 'uuid', 'place_id', 'code']);
			$voucher->update(array_merge($request->except($this->exceptAttributes), [
				'payer_payee_id' => $payer_payee->id,
				'category_id' => $category->id,
			]));

			return $voucher;
		}, 5);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Voucher $voucher) {
		$voucher->delete();
		return response()->json(['message' => 'Xóa phiếu thành công']);
	}
}
