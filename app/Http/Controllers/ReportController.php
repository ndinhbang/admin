<?php

namespace App\Http\Controllers;

use App\Http\Filters\OrderFilter;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    private $start_date = null;
    private $end_date = null;
    private $employee_uuid = null;
    private $category_uuid = [];
    /**
     * Báo cáo doanh số.
     */
    public function revenues(OrderRequest $request) {
        $this->start_date = Carbon::parse(request()->get('start', Carbon::now()))->format('Y-m-d 23:59:59');
        $this->end_date = Carbon::parse(request()->get('end', Carbon::now()))->format('Y-m-d 23:59:59');
        $this->employee_uuid = request()->get('employee_uuid', null);
        $this->category_uuid = request()->get('category_uuid', []);

        $type = request()->get('type', 'order');
        
        switch ($type) {
            case 'order':
                return $this->revenueByOrder($request);
                break;

            case 'product':
                return $this->revenueByProduct($request);
                break;

            case 'cashier':
                return $this->revenueByCashier($request);
                break;
            
            default:
                # code...
                break;
        }
    }

    /**
     * Báo cáo doanh số theo thời gian.
     */
    private function revenueByOrder($request) {
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
                if ($this->employee_uuid) {
                    $query->where('users.uuid', $this->employee_uuid);
                }
            })
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->orderBy('orders.id', 'desc')
            ->first();

        // items Orders
        $items = Order::select('orders.*', 'users.display_name')->join('users', 'users.id', '=', 'orders.creator_id')
            ->where(function ($query) use ($request) {
                if ($this->employee_uuid) {
                    $query->where('users.uuid', $this->employee_uuid);
                }
            })
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->orderBy('orders.id', 'desc')
            ->paginate($request->per_page);

        return response()->json(compact('items', 'stats'));
    }

    /**
     * Báo cáo doanh số theo sản phẩm.
     */
    private function revenueByProduct($request) {

        // stats
        $stats = OrderItem::selectRaw("
                SUM(order_items.total_price) as total_amount,
                SUM(order_items.discount_amount) as total_discount_amount,
                SUM(order_items.discount_order_amount) as total_discount_order_amount,
                SUM(order_items.quantity) as total_quantity
            ")
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where(function ($query) use ($request) {
                if (count($this->category_uuid)) {
                    $query->whereIn('categories.uuid', $this->category_uuid);
                }
            })
            ->whereBetween('order_items.created_at', [$this->start_date, $this->end_date])
            ->first();

        $items = Order::selectRaw("products.*, 
                SUM(order_items.total_price) as total_amount,
                SUM(order_items.discount_amount) as total_discount_amount,
                SUM(order_items.discount_order_amount) as total_discount_order_amount,
                SUM(order_items.quantity) as total_quantity
            ")
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where(function ($query) use ($request) {
                if (count($this->category_uuid)) {
                    $query->whereIn('categories.uuid', $this->category_uuid);
                }
            })
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->orderBy('total_amount', 'desc')
            ->groupBy('products.id')
            ->paginate($request->per_page);

        return response()->json(compact('items', 'stats'));
    }

    /**
     * Báo cáo doanh số theo người bán (thu ngân).
     */
    private function revenueByCashier($request) {

        $stats = [];

        $items = Order::selectRaw("users.*, 
                SUM(orders.amount) as total_amount,
                SUM(orders.discount_amount) as total_discount_amount,
                SUM(orders.discount_items_amount) as total_discount_items_amount,
                SUM(orders.total_dish) as total_quantity
            ")
            ->join('users', 'users.id', '=', 'orders.creator_id')
            ->whereBetween('orders.created_at', [$this->start_date, $this->end_date])
            ->orderBy('total_amount', 'desc')
            ->groupBy('users.id')
            ->paginate($request->per_page);

        return response()->json(compact('items', 'stats'));
    }
}
