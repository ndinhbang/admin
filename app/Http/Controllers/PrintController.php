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

    public function configPrint(PrintRequest $request)
    {
        $place = getBindVal('_currentPlace');
        $place->print_config = $request->config;
        $place->save();

        return response()->json([
			'message' => 'Lưu cấu hình in thành công!',
			'print_config' => $place->print_config,
		]);
    }

    public function configPrinters(PrintRequest $request)
    {
        $place = getBindVal('_currentPlace');
        $place->printers = $request->printers;
		$place->save();

		return response()->json([
			'message' => 'Lưu cấu hình máy in thành công!',
			'printers' => $place->printers,
		]);
    }
}
