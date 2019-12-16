<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    private $start_date = null;
    private $end_date = null;
    /**
     * Báo cáo doanh số.
     */
    public function revenues(OrderRequest $request) {
        $this->start_date = Carbon::parse(request()->get('start', Carbon::now()))->format('Y-m-d 23:59:59');
        $this->end_date = Carbon::parse(request()->get('end', Carbon::now()))->format('Y-m-d 23:59:59');

        $type = request()->get('type', 'time');
        
        switch ($type) {
            case 'time':
                return $this->revenueByTime($request);
                break;
            
            default:
                # code...
                break;
        }
    }

    /**
     * Báo cáo doanh số theo thời gian.
     */
    private function revenueByTime($request) {

        $orders = Order::with([
            'creator',
        ])
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->orderBy('orders.id', 'desc')
            ->paginate($request->per_page);

        $orderStats = Order::selectRaw("
                COUNT(orders.id) as total_order,
                SUM(if(orders.discount_amount > 0,1,0)) as total_discount,
                SUM(if(orders.discount_items_amount > 0,1,0)) as total_discount_items,
                SUM(if(orders.debt > 0,1,0)) as total_debt,
                SUM(orders.amount) as total_amount,
                SUM(orders.discount_amount) as total_discount_amount,
                SUM(orders.discount_items_amount) as total_discount_items_amount,
                SUM(orders.debt) as total_debt_amount
            ")
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->orderBy('orders.id', 'desc')
            ->first();

        return response()->json(compact('orders', 'orderStats'));
    }
}
