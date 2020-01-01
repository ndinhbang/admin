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

                <p class="text-center my-1 mt-3"><strong>ĐƠN HÀNG BÁO BẾP</strong></p>
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
                        </tr>
                        @endforeach
                        @endif
                        <tr>
                            <th class="text-right py-1" colspan="5"></th>
                        </tr>
                    </tbody>
                </table>
                @if($order->note)
                    <p><strong>Ghi chú đơn hàng:</strong><br />{{ $order->note }}</p>
                @endif
            </div>
            <span class="mark">@Goido.NET</span>
        </div>
    </section>

</body>

</html>
