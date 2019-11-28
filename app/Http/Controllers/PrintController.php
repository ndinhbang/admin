<?php

namespace App\Http\Controllers;

use App\Models\Order;

class PrintController extends Controller
{
    public function printOrder(Order $order)
    {
    	$order->load([
            'creator',
            'table',
            'customer',
            'items' => function ($query) {
                $query->orderBy('pivot_id', 'asc');
            },
        ]);

        return view('print.order', [
            'order' => $order
        ]);
    }
}
