<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageController extends Controller {

    private $start_date = null;
    private $end_date = null;

    public function __construct(Request $request) {
        $this->start_date = Carbon::parse(request()->get('start', Carbon::now()))->setTimezone(config('app.timezone'))->format('Y-m-d 00:00:00');
        $this->end_date = Carbon::parse(request()->get('end', Carbon::now()))->setTimezone(config('app.timezone'))->format('Y-m-d 23:59:59');
    }
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function overview(Request $request) {
        // Tổng chi thu theo từng danh mục
        $vouchers = Voucher::select(DB::raw("SUM(amount) as amount_total, SUM(if(vouchers.type='0',amount,0)) as chi_total, SUM(if(vouchers.type='1',amount,0)) as thu_total"))
            ->whereBetween('vouchers.created_at', [$this->start_date, $this->end_date])
            ->first();

		$orderStats = Order::select(DB::raw("
                COUNT(orders.id) as total_order,
                SUM(if(orders.discount_amount > 0,1,0)) as total_discount,
                SUM(if(orders.discount_items_amount > 0,1,0)) as total_discount_items,
                SUM(if(orders.debt > 0,1,0)) as total_debt,
                SUM(orders.amount) as total_amount,
                SUM(orders.discount_amount) as total_discount_amount,
                SUM(orders.discount_items_amount) as total_discount_items_amount,
                SUM(orders.debt) as total_debt_amount,

                SUM(if(orders.state='0',1,0)) as pending,
                SUM(if(orders.is_paid='1',1,0)) as paid,
                SUM(if(orders.is_canceled='1',1,0)) as canceled,
                SUM(if(orders.is_returned='1',1,0)) as returned
            "))
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
			->orderBy('orders.id', 'desc')
			->first();

		return response()->json(compact('vouchers', 'orderStats'));
	}

    public function dailyRevenues(Request $request) {
        $startDate = $this->start_date;
        if(Carbon::parse($this->start_date)->diffInDays($this->end_date) < 7)
            $startDate = Carbon::parse($this->end_date)->setTimezone(config('app.timezone'))->subDays(7)->format('Y-m-d 00:00:00');

        $dailyRevenues = Order::selectRaw("
                DATE(orders.created_at) as days,
                SUM(orders.amount) as total_amount
            ")
            ->whereBetween('orders.created_at', [$startDate, $this->end_date])
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->get();

        return response()->json(compact('dailyRevenues'));
    }
}
