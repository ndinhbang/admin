<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrintRequest;
use App\Models\Order;
use App\Models\OrderItem;

class PrintController extends Controller
{
    public function preview(PrintRequest $request, Order $order)
    {
        $itemId = $request->item_id ?? null;
        $stt = $request->stt ?? '';

        $template = $request->template ?? ($itemId ? 'pos58' : 'pos80');
        $order->load([
            'table',
            'items' => function ($query) use ($itemId) {
                if ($itemId) {
                    $query->where('order_items.id', $itemId);
                }
            },
        ]);

        if (!$itemId) {
            $order->load([
                'place',
                'creator',
                'customer',
            ]);
        }

        $data = [
            'order' => $order
        ];

        if ($itemId) {
            $data = array_merge($data, [
                'item'  => $order->items->first() ?? null,
                'stt'   => $stt
            ]);
        }

        return view('print.' . $template, $data);
    }
}
