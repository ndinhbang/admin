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
                <p class="text-center my-1"><strong>@{{ title }}</strong></p>
                @{{#if address}}
                    <p class="text-center my-1">@{{ address }}</p>
                @{{/if}}
                @{{#if address}}
                <p class="text-center my-1">@{{ contact_phone }}</p>
                @{{/if}}
                @{{/place}}
                @{{#order}}
                    <p class="text-center my-1 mt-3"><strong>HÓA ĐƠN BÁN HÀNG</strong></p>
                    <p class="text-center my-1 mb-3"><strong>(TẠM TÍNH)</strong></p>
                    <p class="my-1"><strong>Bàn:</strong> @{{ area_name }}-@{{ table_name }} | @{{ card_name }}
                    </p>
                    <p class="my-1"><strong>Giờ vào: </strong>
                        <span id="time">@{{ created_at }}</span>
                    </p>
                    <p class="my-1"><strong>Giờ ra: </strong>
                        <span id="time">@{{ updated_at }}</span>
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
                            <td class="text-left top-border p-0 pt-1" colspan="4">
                                <span>@{{ product_name }}</span>
                                @{{#if children}}
                                <div><small>+</small>
                                    @{{#each children}}
                                        <small><em>@{{product_name}}; </em></small>
                                    @{{/each}}
                                    <small>[ @{{money ../children_price}} ]</small>
                                </div>
                                @{{/if}}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-left p-0 pb-1">@{{money product_price}}</td>
                            <td class="text-right p-0 pb-1">@{{ quantity }}</td>
                            <td class="text-right p-0 pb-1">@{{money discount_amount}} (@{{js "Math.round((this.discount_amount/(this.simple_price+this.discount_amount)) * 100)"}}%)</td>
                            <td class="text-right p-0 pb-1">
                                <strong>@{{money total_price}}</strong>
                            </td>
                        </tr>
                        @{{/each}}
                        <tr>
                            <th class="text-right py-1" colspan="5"> </th>
                        </tr>
                        <tr>
                            <td class="text-left py-1" colspan="3"><strong>Tổng tiền hàng: </strong></td>
                            <td class="text-right py-1" colspan="2">
                                <strong>@{{total amount discount_amount }}</strong></td>
                        </tr>
                        @{{#if discount_amount}}
                        <tr>
                            <td class="text-left pb-1" colspan="3"><strong>Giảm giá theo đơn: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">@{{money discount_amount}} (@{{js "Math.round((this.discount_amount/(this.amount+this.discount_amount)) * 100)"}}%)</td>
                        </tr>
                        @{{/if}}
                        <tr>
                            <td class="text-left pb-1" colspan="3"><strong>Tổng thanh toán: </strong></td>
                            <td class="text-right py-0 pb-1" colspan="2">
                                <strong>@{{money amount}}</strong></td>
                        </tr>
                        @{{#if note}}
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="5">Ghi chú:</td>
                        </tr>
                        <tr>
                            <td class="text-left py-0 pb-1" colspan="5">@{{ note }}</td>
                        </tr>
                        @{{/if}}
                    </tbody>
                </table>
            </div>
            @{{/order}}
            <span class="mark">@Goido.NET</span>
        </div>
    </section>

</body>

</html>
