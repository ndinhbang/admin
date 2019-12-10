<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        <?php include(public_path().'/css/print.css'); ?>
    </style>
</head>

<body class="receipt w80mm">
    <section class="sheet p-0">
        <div class="print">
            {{-- <div class="print-logo">
                <img src="/images/logo.svg">
            </div> --}}
            @{{#order}}
            <div class="print-header">
                @{{#place}}
                <p class="my-1"><strong>@{{ title }}</strong></p>
                <p class="my-1">Địa chỉ: @{{ address }}</p>
                <p class="my-1">Liên hệ: @{{ contact_phone }}</p>
                @{{/place}}

                <p class="text-center my-1 mt-3"><strong>Hóa đơn thanh toán</strong></p>
                <p class="text-center my-1 mb-3"><strong>@{{ code }}</strong></p>
                <p class="my-1"><strong>Bàn:</strong>
                    @{{#table}}
                    <span id="computer">@{{ name }}</span>
                    @{{/table}}
                </p>
                <p class="my-1"><strong>Thời gian:</strong>
                    <span id="time">@{{ created_at }}</span>
                </p>
                <p class="my-1"><strong>Nhân viên:</strong>
                    @{{#creator}}
                    <span id="staff">@{{ display_name }}</span>
                    @{{/creator}}
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
                        @{{#each items}}
                        <tr>
                            <td class="text-left" colspan="4"><strong>@{{incremented @index}}.</strong> @{{ name }}</td>
                        </tr>
                        <tr>
                            <td class="text-left">@{{ price }}</td>
                            <td class="text-right">@{{ quantity }}</td>
                            <td class="text-right">>@{{ discount_amount }}</td>
                            <td class="text-right">
                                <strong>@{{ total_price }}</strong></td>
                        </tr>
                        @{{/each}}
                        <tr>
                            <th class="text-right" colspan="4"></th>
                        </tr>
                        <tr>
                            <td class="text-left pb-1" colspan="2"><strong>Tổng tiền hàng: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>@{{ amount }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="2"><strong>Khách trả: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>@{{ received_amount }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="2"><strong>Đã thanh toán: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>@{{ paid }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="4">Ghi chú:</td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="4">@{{ note }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @{{/order}}
            <span class="mark">@goido.net</span>
        </div>
    </section>

</body>

</html>