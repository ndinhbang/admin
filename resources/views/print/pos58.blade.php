<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/print.css" rel="stylesheet">
</head>

<body class="receipt w50mm">
    <section class="sheet padding-5mm">
        <div class="print p-0">
            {{-- <div class="print-logo">
                <img src="/images/logo.svg">
            </div> --}}
            <div class="print-header">
                <table>
                    <thead>
                        <tr>
                            <td class="text-left p-0"><strong>Bàn:</strong> {{ $order->table->name ?? '' }}</td>
                            <td class="text-right p-0">{{ $stt }}/{{ $item->pivot->quantity ?? 0 }}</td>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="order-info">
                <table>
                    <thead>
                        <tr>
                            <td class="text-left p-0"><strong>{{ $item->name }}</strong></td>
                            <td class="text-right p-0">{{ number_format($item->price ?? 0, 0, ',', '.')
                            }}</td>
                        </tr>
                    </thead>
                </table>
            </div>
            <span class="mark">@goido.net</span>
        </div>
    </section>

</body>

</html>
