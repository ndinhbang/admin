<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/print.css" rel="stylesheet">
    <style>
        @page {
            size: auto
        }

        /* output size */
        body.receipt .sheet {
            width: auto
        }

        /* sheet size */
        @media print {
            body.receipt {
                width: auto
            }
        }

        /* fix for Chrome */
    </style>
</head>

<body class="receipt">
    <section class="sheet padding-5mm">
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
