<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Resources\PosOrdersCollection;
use App\Models\Order;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PosReportController extends Controller
{
    private $start_date = null;
    private $end_date = null;
    private $code = 'this_month';
    private $employee_uuid = [];
    private $employee_id = [];
    private $category_uuid = [];

    public function __construct(Request $request) {
        $this->start_date = Carbon::parse(request()->get('start', Carbon::now()))->setTimezone(config('app.timezone'))->format('Y-m-d 00:00:00');
        $this->end_date = Carbon::parse(request()->get('end', Carbon::now()))->setTimezone(config('app.timezone'))->format('Y-m-d 23:59:59');
        $this->code = request()->get('code', 'this_month');
        $this->employee_uuid = request()->get('employee_uuid', []);
        $this->category_uuid = request()->get('category_uuid', []);
    }

    /**
     * Báo cáo doanh số theo đơn hàng.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\PosOrdersCollection
     * @throws \Exception
     */
    public function index(Request $request) {
        // stats
        $stats = Order::selectRaw("
                COUNT(orders.id) as total_order,
                SUM(if(orders.discount_amount > 0,1,0)) as total_discount,
                SUM(if(orders.discount_items_amount > 0,1,0)) as total_discount_items,
                SUM(if(orders.debt > 0,1,0)) as total_debt,
                SUM(orders.amount) as total_amount,
                SUM(orders.discount_amount) as total_discount_amount,
                SUM(orders.discount_items_amount) as total_discount_items_amount,
                SUM(orders.debt) as total_debt_amount
            ")
            ->join('users', 'users.id', '=', 'orders.creator_id')
            ->where(function ($query) use ($request) {
                if (count($this->employee_uuid)) {
                    $query->whereIn('users.uuid', $this->employee_uuid);
                }
            })
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->where('orders.is_paid', true)
            ->orderBy('orders.id', 'desc')
            ->first();

        // items Orders
        if (count($this->employee_uuid)) {
            $this->employee_id = User::whereIn('users.uuid', $this->employee_uuid)->pluck('id');
        }
        $orders  = Order::with(
            [
                'creator',
                'customer',
                'table.area',
                'items' => function ($query) {
                    $query->where('parent_id', 0);
                },
                'items.children.product.category',
                'items.product.category',
            ]
        )
            ->where(function ($query) use ($request) {
                if (count($this->employee_id)) {
                    $query->whereIn('orders.creator_id', $this->employee_id);
                }
            })
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->orderBy('orders.id', 'desc')
            ->withTrashed()
            ->paginate($request->per_page);

        return ( new PosOrdersCollection($orders) )->using(
            [
                'place_uuid' => currentPlace()->uuid,
            ]
        )->additional([ 'stats' => $stats ]);
//        return response()->json(compact('orders', 'stats'));
    }
}
