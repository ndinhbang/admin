<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        <?php include( public_path() . '/css/print.css' ); ?>
    </style>
</head>

<body class="receipt w74mm">
<section class="sheet padding-5mm">
    <div class="print">
        {{-- <div class="print-logo">
            <img src="/images/logo.svg">
        </div> --}}

        <div class="print-header">
            @php($isShowLogo = $config_print['receipt']['show_logo'] ?? true)
            @if($isShowLogo && $order->place->logo)
                <p class="print-logo my-0"><img src="{{ env('APP_MEDIA_URL').'/places/'.$order->place->logo }}"/></p>
            @endif
            @if (!is_null($print_info))
                <h1 class="text-center my-0"><strong>{{ $print_info['title'] }}</strong></h1>
                @if($print_info['address'])
                    <p class="my-1 text-center">{{ $print_info['address'] }}</p>
                @endif
                @if($print_info['phone'])
                    <p class="my-1 text-center">{{ $print_info['phone'] }}</p>
                @endif
            @endif
            <h2 class="text-center my-1 mt-3"><strong>HÓA ĐƠN BÁN HÀNG</strong></h2>
            <p class="text-center my-1 mb-3">Số HĐ: <strong>{{ $order->code }}</strong></p>
            <p class="my-1"><strong>Bàn:</strong>
                <span
                    id="computer">{{ $order->table->area->name ?? '' }}-{{ $order->table->name ?? 'Mang về' }} | {{ $order->card_name }}</span>
            </p>
            <table>
                <tbody>
                <tr>
                    <td class="text-left pb-1">
                        <strong>Giờ vào:</strong><br/>
                        <span id="time">{{ Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i:s') }}</span>
                    </td>
                    <td class="text-left pb-1">
                        <strong>Giờ ra:</strong><br/>
                        <span id="time">{{ Carbon\Carbon::parse($order->updated_at)->format('d/m/Y H:i:s') }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="my-1">
            </p>
            <p class="my-1">
            </p>
            <p class="my-1"><strong>Nhân viên:</strong>
                <span id="staff">{{ $order->creator->display_name ?? '' }}</span>
            </p>
        </div>
        <div class="order-info">
            <table>
                <thead>
                <tr>
                    <th class="text-left">Đơn giá</th>
                    <th class="text-right">SL</th>
                    <th class="text-right">Giảm giá</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
                </thead>
                <tbody>
                @if ($items = $order->items ?? [])
                    @foreach ($items as $key => $item)
                        <tr>
                            <td class="text-left top-border p-0 pt-1" colspan="4">
                                <span class="">{{ $item->product->name }}</span>
                                @if($item->note)
                                    <div><small><em>{{ $item->note }}</em></small></div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left p-0 pb-1">
                                {{ number_format($item->product->price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-right p-0 pb-1">
                                @if($item->price_by_time)
                                    {{ Carbon\Carbon::parse($item->time_in)->format('d/m/Y H:i:s') }}<br/>
                                    {{ Carbon\Carbon::parse($item->time_out)->format('d/m/Y H:i:s') }}<br/>
                                    = <strong>{{ secondsToTime($item->quantity*60*60) }}</strong>
                                @else
                                    {{ $item->quantity }}
                                @endif
                            </td>
                            <td class="text-right p-0 pb-1">
                                {{ number_format($item->discount_amount ?? 0, 0, ',', '.') }}
                                @if($item->discount_amount > 0)
                                    <small>({{ round(($item->discount_amount/($item->total_price+$item->discount_amount)
                                )*100) }}%)</small>
                                @else
                                    <small>(0%)</small>
                                @endif
                            </td>
                            <td class="text-right p-0 pb-1">
                                <strong>{{ number_format($item->total_price, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <th class="text-right py-1" colspan="5"></th>
                </tr>
                @php($total_amount = number_format(round($order->amount+$order->discount_amount, -2), 0, ',', '.'))
                @if($order->discount_amount)
                    <tr>
                        <td class="text-right pb-1" colspan="3"><strong>Giảm giá theo đơn: </strong></td>
                        <td class="text-right pb-1" colspan="2">
                            <strong>-{{ $discount_amount = number_format(round($order->discount_amount, -2), 0, ',', '.') }}</strong>
                            <small>({{ round(($discount_amount/$total_amount)*100, 2) }}%)</small>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td class="text-right pb-1 fs12" colspan="3"><strong>Thành tiền: </strong></td>
                    <td class="text-right pb-1 fs12" colspan="2">
                        <strong>{{ number_format(round($order->amount, -2), 0, ',', '.') }}</strong></td>
                </tr>
                </tbody>
            </table>
            @if($order->note)
                <p><strong>Ghi chú đơn hàng:</strong><br/>{{ $order->note }}</p>
            @endif
        </div>
        @if (!is_null($print_info))
            <hr/>
            @if($print_info['note'])
                <p class="text-center">{{ $print_info['note'] }}</p>
            @endif
            @if($print_info['goodbye'])
                <h3 class="text-center"><strong><em>{{ $print_info['goodbye'] }}</em></strong></h3>
            @endif
        @endif
        <span class="mark">@Goido.NET</span>
    </div>
</section>

</body>

</html>
