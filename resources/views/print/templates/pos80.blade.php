<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        <?php include(public_path().'/css/print.css'); ?>
    </style>
</head>

<body class="receipt w74mm">
    <section class="sheet padding-5mm">
        <div class="print">
            {{-- <div class="print-logo">
                <img src="/images/logo.svg">
            </div> --}}

            <div class="print-header">
                @{{#place}}
                <p class="my-1"><strong>@{{ title }}</strong></p>
                <p class="my-1">Địa chỉ: @{{ address }}</p>
                <p class="my-1">Liên hệ: @{{ contact_phone }}</p>
                @{{/place}}
                @{{#order}}
                    <p class="text-center my-1 mt-3"><strong>HÓA ĐƠN BÁN HÀNG</strong></p>
                    <p class="text-center my-1 mb-3"><strong>(TẠM TÍNH)</strong></p>
                    @{{#table}}
                    <p class="my-1"><strong>Bàn: </strong>
                        <span id="computer">@{{ name }}</span>
                    </p>
                    @{{/table}}
                    <p class="my-1"><strong>Thời gian: </strong>
                        <span id="time">@{{ created_at }}</span>
                    </p>
                @{{/order}}
                @{{#creator}}
                <p class="my-1"><strong>Nhân viên: </strong>
                    <span id="staff">@{{ display_name }}</span>
                </p>
                @{{/creator}}
            </div>
            @{{#order}}
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
                            @{{#product}}
                            <td class="text-left" colspan="4"><strong>@{{incremented @index}}.</strong> @{{ name }}</td>
                            @{{/product}}
                        </tr>
                        <tr>
                            @{{#product}}
                            <td class="text-left">@{{money price}}</td>
                            @{{/product}}
                            <td class="text-right">@{{ quantity }}</td>
                            <td class="text-right">@{{money discount_amount}}</td>
                            <td class="text-right">
                                <strong>@{{money simple_price}}</strong>
                            </td>
                        </tr>
                        @{{/each}}
                        <tr>
                            <th class="text-right p-0" colspan="4"></th>
                        </tr>
                        <tr>
                            <td class="text-left pb-1" colspan="2"><strong>Tổng tiền hàng: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>@{{total amount discount_amount }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left pb-1" colspan="2"><strong>Khuyến mãi: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">@{{money discount_amount}}</td>
                        </tr>
                        <tr>
                            <td class="text-left pb-1" colspan="2"><strong>Tổng thanh toán: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>@{{money amount}}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="2"><strong>Nhận của khách: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>@{{money received_amount}}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="2"><strong>Trả lại khách: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>@{{payback received_amount amount}}</strong></td>
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
            <span class="mark">@Goido.NET</span>
        </div>
    </section>

</body>

</html>
