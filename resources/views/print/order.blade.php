<!DOCTYPE html>
<html lang="en">
<head>
    <link href="/css/print.css" rel="stylesheet">
    <style>
        @page { size: auto } /* output size */
        body.receipt .sheet { width: auto } /* sheet size */
        @media print { body.receipt { width: auto } } /* fix for Chrome */
    </style>
</head>
<body class="receipt">
<section class="sheet padding-5mm">
    <div class="print">
        {{-- <div class="print-logo">
            <img src="/images/logo.svg">
        </div> --}}
        <div class="print-header">
            <p><strong>Bàn:</strong>
                <span id="computer">{{ $order->table->name ?? '' }}</span>
            </p>
            <p><strong>Thời gian:</strong>
                <span id="time">{{ $order->created_at }}</span>
            </p>
            <p><strong>Nhân viên:</strong>
                <span id="staff">{{ $order->creator->display_name ?? '' }}</span>
            </p>
        </div>
        <div class="order-info">
            <table>
                <thead>
                    <tr>
                        <th class="text-left">Sản phẩm</th>
                        <th class="text-right nowrap">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</section>
</body>
</html>
