<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/print.css" rel="stylesheet">
</head>

<body class="receipt w50mm">
    <section class="sheet p-0">
        <div class="print">
            {{-- <div class="print-logo">
                <img src="/images/logo.svg">
            </div> --}}

            <div class="print-header">
                <table>
                    <thead>
                        <tr>
                            <td class="text-left"><strong>Bàn:</strong> {{ $order->table->name ?? '' }}</td>
                            <td class="text-right">{{ $stt }}/{{ $item->pivot->quantity ?? 0 }}</td>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="order-info">
                <table>
                    <thead>
                        <tr>
                            <td class="text-left"><strong>{{ $item->name }}</strong></td>
                        </tr>
                    </thead>
                </table>
            </div>
            <span class="mark">@goido.net</span>
        </div>
    </section>

</body>

</html>
