<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrintRequest;
use App\Models\Order;
use App\Models\OrderItem;

class PrintController extends Controller
{
    public function preview(PrintRequest $request, Order $order)
    {
        $template = $request->template ?? 'pos80';
        $order->load([
            'place',
            'creator',
            'customer',
            'table',
            'items',
            'items.product',
        ]);

        $data = [
            'order' => $order,
            'print_info'  => $order->place->print_info ?? null,
        ];

        return view('print.' . $template, $data);
    }
}
