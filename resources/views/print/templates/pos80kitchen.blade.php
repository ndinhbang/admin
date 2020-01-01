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
                        <p class="text-center my-1 mb-3"><strong>@{{ code }}</strong></p>
                    @{{/if}}
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
                            <th class="text-left">TT</th>
                            <th class="text-left">Tên hàng</th>
                            <th class="text-right">SL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @{{#each items}}
                        <tr>
                            @{{#product}}
                            <td class="text-left"><strong>@{{incremented @index}}</strong></td>
                            <td class="text-left">@{{ name }}</td>
                            <td class="text-left">@{{ quantity }}</td>
                            @{{/product}}
                        </tr>
                        @{{/each}}
                        <tr>
                            <th class="text-right p-0" colspan="4"></th>
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
