<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/print.css?v=4" rel="stylesheet">
</head>

<body class="receipt w74mm">
    <section class="sheet padding-5mm">
        <div class="print">
            {{-- <div class="print-logo">
                <img src="/images/logo.svg">
            </div> --}}

            <div class="print-header">
                @if($order->place->logo)
                    <p class="print-logo"><img src="{{ env('APP_MEDIA_URL').'/places/'.$order->place->logo }}" /></p>
                @endif
                @if (!is_null($print_info))
                    <h1 class="text-center mb-0"><strong>{{ $print_info['title'] }}</strong></h1>
                    @if($print_info['address'])
                        <p class="my-1 text-center">{{ $print_info['address'] }}</p>
                    @endif
                    @if($print_info['phone'])
                        <p class="my-1 text-center">{{ $print_info['phone'] }}</p>
                    @endif
                @endif
                <br />
                <h2 class="text-center my-1 mt-3"><strong>HÓA ĐƠN BÁN HÀNG</strong></h2>
                <p class="text-center my-1 mb-3">Số HĐ: <strong>{{ $order->code }}</strong></p>
                <p class="my-1"><strong>Bàn:</strong>
                    <span id="computer">{{ $order->table->name ?? '' }}</span>
                </p>
                <p class="my-1"><strong>Thời gian:</strong>
                    <span id="time">{{ $order->created_at }}</span>
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
                            <td class="text-left top-border pb-1 pt-2 fs12" colspan="4">
                                <strong class=" fs13">{{ $item->product->name }}</strong>
                                @if($item->note)
                                <div><small><em>{{ $item->note }}</em></small></div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left pt-1 fs12">
                                {{ number_format($item->product->price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-right pt-1 fs12">{{ $item->quantity }}</td>
                            <td class="text-right pt-1 fs12">{{ number_format($item->discount_amount ?? 0, 0, ',', '.') }} <small>({{ ($item->discount_amount/$item->total_price)*100 }}%)</small>
                            </td>
                            <td class="text-right pt-1 fs12">
                                <strong>{{ number_format($item->total_price, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <th class="text-right py-1" colspan="5"></th>
                        </tr>
                        <tr>
                            <td class="text-right pb-1 fs13" colspan="3"><strong>Tiền hàng: </strong></td>
                            <td class="text-right pb-1 fs13" colspan="2">
                                <strong>{{ $total_amount = number_format(round($order->amount+$order->discount_amount, -2), 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-right pb-1 fs13" colspan="3"><strong>Giảm giá theo đơn: </strong></td>
                            <td class="text-right pb-1 fs13" colspan="2">
                                <strong>-{{ $discount_amount = number_format(round($order->discount_amount, -2), 0, ',', '.') }}</strong><br />
                                <small>({{ round(($discount_amount/$total_amount)*100, 2) }}%)</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right pb-1 fs16" colspan="3"><strong>Thành tiền: </strong></td>
                            <td class="text-right pb-1 fs16" colspan="2">
                                <strong>{{ number_format(round($order->amount, -2), 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-right pt-3 py-1 fs13 top-border" colspan="3"><strong>Tiền khách đưa: </strong></td>
                            <td class="text-right pt-3 py-1 fs13 top-border" colspan="2">
                                <strong>{{ number_format($order->received_amount ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-right py-1 fs13" colspan="3"><strong>Tiền thừa: </strong></td>
                            <td class="text-right py-1 fs13" colspan="2">
                                <strong>{{ number_format(round($order->received_amount-$order->amount, -2) ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
                @if($order->note)
                    <p><strong>Ghi chú đơn hàng:</strong><br />{{ $order->note }}</p>
                @endif
            </div>
            @if (!is_null($print_info))
                <hr />
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
