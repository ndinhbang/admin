<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrintRequest;
use App\Models\Place;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrintController extends Controller
{
    private $start_date = null;
    private $end_date = null;
    private $code = 'this_month';
    private $employee_uuid = [];
    private $category_uuid = [];

    public function __construct(Request $request) {
        $this->start_date = Carbon::parse(request()->get('start', Carbon::now()))->setTimezone(config('app.timezone'))->format('Y-m-d 00:00:00');
        $this->end_date = Carbon::parse(request()->get('end', Carbon::now()))->setTimezone(config('app.timezone'))->format('Y-m-d 23:59:59');
        $this->code = request()->get('code', 'this_month');
        $this->employee_uuid = request()->get('employee_uuid', []);
        $this->category_uuid = request()->get('category_uuid', []);
    }

    public function preview(PrintRequest $request, Order $order)
    {
        $template = $request->template ?? 'pos80';
        $template = $request->local ? 'templates.'.$template : $template;
        $order->load([
            'place',
            'creator',
            'customer',
            'table',
            'items' => function ($query) {
                $query->where('parent_id', 0);
            },
            'items.children.product',
            'items.product',
        ]);

        $data = [
            'order' => $order,
            'print_info'  => $order->place->print_info ?? null,
            'config_print' => $order->place->config_print ?? null,
        ];

        return view('print.' . $template, $data);
    }

    public function report(PrintRequest $request, Place $place)
    {
        // Báo cáo theo Đơn hàng
        $orderStats = Order::selectRaw("
                COUNT(orders.id) as total_order,
                SUM(orders.total_dish) as total_dish,
                SUM(if(orders.discount_amount > 0,1,0)) as total_discount,
                SUM(if(orders.discount_items_amount > 0,1,0)) as total_discount_items,
                SUM(if(orders.debt > 0,1,0)) as total_debt,
                SUM(orders.amount) as total_amount,
                SUM(orders.discount_amount) as total_discount_amount,
                SUM(orders.discount_items_amount) as total_discount_items_amount,
                SUM(orders.debt) as total_debt_amount,
                SUM(if(orders.kind=0,orders.amount,0)) as takeaway_amount,
                SUM(if(orders.kind=1,orders.amount,0)) as inplace_amount
            ")
            ->join('users', 'users.id', '=', 'orders.creator_id')
            ->where(function ($query) use ($request) {
                if (count($this->employee_uuid)) {
                    $query->whereIn('users.uuid', $this->employee_uuid);
                }
            })
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->where('orders.is_paid', 1)
            ->where('orders.place_id', $place->id)
            ->first();

        // Báo cáo theo danh mục
        $categoryStats = OrderItem::selectRaw("
                categories.*,
                SUM(order_items.total_price) as total_amount,
                SUM(order_items.discount_amount) as total_discount_amount,
                SUM(order_items.discount_order_amount) as total_discount_order_amount,
                SUM(order_items.quantity) as total_quantity
            ")
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('users', 'users.id', '=', 'orders.creator_id')
            ->where(function ($query) use ($request) {
                if (count($this->employee_uuid)) {
                    $query->whereIn('users.uuid', $this->employee_uuid);
                }
            })
            ->whereBetween('order_items.created_at', [$this->start_date, $this->end_date])
            ->where('products.place_id', $place->id)
            ->where('orders.is_paid', 1)
            ->groupBy('categories.id')
            ->get();

        // Báo cáo theo người bán
        $userStats = Order::selectRaw("users.*, 
                SUM(orders.amount) as total_amount,
                SUM(orders.discount_amount) as total_discount_amount,
                SUM(orders.discount_items_amount) as total_discount_items_amount,
                SUM(orders.total_dish) as total_quantity
            ")
            ->join('users', 'users.id', '=', 'orders.creator_id')
            ->where(function ($query) use ($request) {
                if (count($this->employee_uuid)) {
                    $query->whereIn('users.uuid', $this->employee_uuid);
                }
            })
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->orderBy('total_amount', 'desc')
            ->where('orders.place_id', $place->id)
            ->where('orders.is_paid', 1)
            ->groupBy('users.id')
            ->get();

        $endDate = $this->end_date;
        if(Carbon::parse($this->end_date)->isToday())
            $endDate = Carbon::now();

        $data = [
            'place'         => $place,
            'orderStats'    => $orderStats,
            'categoryStats' => $categoryStats,
            'userStats'     => $userStats,
            'start_date'    => $this->start_date,
            'end_date'      => $endDate,
            'print_info'    => $place->print_info ?? null,
        ];

        return view('print.pos80report', $data);
    }
}
