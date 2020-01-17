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
                @{{#order}}
                    <p class="text-center my-1 mt-3"><strong>ĐƠN HÀNG BÁO BẾP</strong></p>
                    @{{#if code}}
                        <p class="text-center my-1 mb-1">@{{ code }}</p>
                    @{{/if}}

                    @{{#if table_name}}
                        <div align="center" class="my-3">
                            <h1 align="center" class="my-1">@{{ area_name }}-@{{ table_name }} | @{{ card_name }}</h1>
                        </div>
                    @{{else}}
                        <div align="center" class="my-3">
                            <h1 align="center" class="my-1">Mang về | @{{ card_name }}</h1>
                        </div>
                    @{{/if}}
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
                            @{{#each items}}
                                @{{#if printed_qty}}
                                <tr>
                                    <td class="text-left top-border"><h4 class="py-1 my-1">@{{incremented @index}}</h4></td>
                                    <td class="text-left top-border">
                                        <h2 class="py-1 my-1">@{{ product_name }}</h2>

                                        @{{#if note}}
                                            <div><em>Ghi chú:</em> <strong>@{{ note }}</strong></div>
                                        @{{/if}}
                                    </td>
                                    <td class="text-left top-border"><h1 class="py-1 my-1">@{{ printed_qty }}</h1></td>
                                </tr>
                                @{{/if}}
                            @{{/each}}
                            <tr>
                                <th class="text-right p-0" colspan="3"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @{{#if note}}
                    <p><strong>Ghi chú đơn hàng:</strong><br />@{{ note }}</p>
                @{{/if}}

            @{{/order}}
            <span class="mark">
                @{{#printer}}
                    <span>@{{ title }}</span>
                @{{/printer}}
            - @Goido.NET</span>
        </div>
    </section>

</body>

</html>
