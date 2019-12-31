<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/print.css" rel="stylesheet">
</head>

<body class="receipt w74mm">
    <section class="sheet padding-5mm">
        <div class="print">
            {{-- <div class="print-logo">
                <img src="/images/logo.svg">
            </div> --}}

            <div class="print-header">
                @if ($order->place)
                <p class="my-1"><strong>{{ $order->place->title }}</strong></p>
                <p class="my-1">Địa chỉ: {{ $order->place->address }}</p>
                <p class="my-1">Liên hệ: {{ $order->place->contact_phone }}</p>
                @endif

                <p class="text-center my-1 mt-3"><strong>Hóa đơn thanh toán</strong></p>
                <p class="text-center my-1 mb-3"><strong>{{ $order->code }}</strong></p>
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
                            <td class="text-left" colspan="4"><strong>{{ $key +1 }}.</strong> {{ $item->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-left">{{ number_format($item->product->price, 0, ',', '.') }}</td>
                            <td class="text-right">{{ $item->pivot->quantity }}</td>
                            <td class="text-right">{{ number_format($item->pivot->discount_amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-right">
                                <strong>{{ number_format($item->pivot->total_price, 0, ',', '.') }}</strong></td>
                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <th class="text-right p-0" colspan="4"></th>
                        </tr>
                        <tr>
                            <td class="text-left pb-1" colspan="2"><strong>Tổng tiền hàng: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>{{ number_format($order->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="2"><strong>Khách trả: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>{{ number_format($order->received_amount ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="2"><strong>Đã thanh toán: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>{{ number_format($order->paid ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="4">Ghi chú:</td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="4">{{ $order->note }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <span class="mark">@goido.net</span>
        </div>
    </section>

</body>

</html>
