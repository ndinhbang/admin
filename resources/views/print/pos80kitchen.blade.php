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

                <p class="text-center my-1 mt-3">
                    <strong>ĐƠN HÀNG BÁO BẾP</strong><br />
                    {{ $order->code }}
                </p>
                <div align="center" class="my-3">
                    <h1 align="center" class="my-1">{{ $order->table->name ?? 'Mang về' }}</h1>
                </div>
                <p class="my-1"><strong>Thời gian:</strong>
                    <span id="time">{{ $order->updated_at }}</span>
                </p>
                <p class="my-1"><strong>Nhân viên:</strong>
                    <span id="staff">{{ $order->creator->display_name ?? '' }}</span>
                </p>
            </div>
            <div class="order-info" style="margin-bottom: 60px;">
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
                            @if($item->printed_qty > 0)
                                <tr>
                                    <td class="text-left top-border"><h4 class="py-1 my-1">{{ $key +1 }}</h4></td>
                                    <td class="text-left top-border">
                                        <h2 class="py-1 my-1">{{ $item->product->name }}</h2>
                                        @if($item->note)
                                            <div><em>Ghi chú:</em> <strong>{{ $item->note }}</strong></div>
                                        @endif
                                    </td>
                                    <td class="text-right top-border"><h1 class="py-1 my-1">{{ $item->printed_qty }}</h1></td>
                                </tr>
                            @endif
                        @endforeach
                        @endif
                        <tr>
                            <th class="text-right py-1" colspan="3"></th>
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
