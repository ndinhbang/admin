<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/print.css?v=2" rel="stylesheet">
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

                <p class="text-center my-1 mt-3"><strong>HÓA ĐƠN BÁN HÀNG</strong></p>
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
                            <th class="text-left">TT</th>
                            <th class="text-left">Tên hàng</th>
                            <th class="text-right">SL</th>
                            <th class="text-right">Giảm giá</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($items = $order->items ?? [])
                        @foreach ($items as $key => $item)
                        <tr>
                            <td class="text-left top-border">{{ $key +1 }}</td>
                            <td class="text-left top-border">
                                {{ $item->product->name }}
                                @if($item->note)
                                <div><em>{{ $item->note }}</em></div>
                                @endif
                            </td>
                            <td class="text-right top-border">{{ $item->quantity }}</td>
                            <td class="text-right top-border">{{ number_format($item->discount_amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-right top-border">
                                <strong>{{ number_format($item->total_price, 0, ',', '.') }}</strong></td>
                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <th class="text-right py-1" colspan="5"></th>
                        </tr>
                        <tr>
                            <td class="text-left pb-1" colspan="3"><strong>Tổng tiền hàng: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>{{ number_format($order->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-1" colspan="3"><strong>Khách trả: </strong></td>
                            <td class="text-right py-1" colspan="2">
                                <strong>{{ number_format($order->received_amount ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-1" colspan="3"><strong>Đã thanh toán: </strong></td>
                            <td class="text-right py-1" colspan="2">
                                <strong>{{ number_format($order->paid ?? 0, 0, ',', '.') }}</strong></td>
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
