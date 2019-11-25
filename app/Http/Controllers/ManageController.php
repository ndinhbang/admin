<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageController extends Controller {

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function overview(Request $request) {
		// tổng thu/chi
		$vouchers = Voucher::select(DB::raw("
                SUM(if(vouchers.type='0',amount,0)) as chi_amount,
                SUM(if(vouchers.type='1',amount,0)) as thu_amount,
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

		$orders = Order::select(DB::raw("
                COUNT(*) as total,
                SUM(if(orders.state='0',1,0)) as pending,
                SUM(if(orders.is_paid='1',1,0)) as paid,
                SUM(if(orders.is_canceled='1',1,0)) as canceled,
                SUM(if(orders.is_returned='1',1,0)) as returned
            "))
			->where(function ($query) use ($request) {
				// date time range
				$startDate = Carbon::parse($request->get('start', Carbon::now()))->format('Y-m-d 00:00:00');
				$endDate = Carbon::parse($request->get('end', Carbon::now()))->format('Y-m-d 23:59:59');

				$query->whereBetween('created_at', [$startDate, $endDate]);
			})
			->orderBy('orders.id', 'desc')
			->first();

		return response()->json(compact('vouchers', 'orders'));
	}
}
