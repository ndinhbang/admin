<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        <?php include(public_path().'/css/print.css'); ?>
    </style>
</head>

<body class="receipt w50mm">
        <section class="sheet padding-5mm">
            <div class="print p-0">
                <div class="print-header m-0 p-0">
                    <table>
                        <thead>
                            <tr>
                                <td class="text-left p-0"><strong>Bàn:</strong> @{{ table_name }}</td>
                                <td class="text-right p-0">@{{ stt }}</td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="order-info">
                    <table>
                        <thead>
                            <tr>
                                <td class="text-left p-0"><strong>@{{ item_name }}</strong></td>
                                <td class="text-right p-0">@{{ item_price }}</td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <span class="mark">@goido.net</span>
            </div>
        </section>

    </body>

</html>
