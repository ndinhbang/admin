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
            <div class="print">
                <div class="print-header my-0 mb-3">
                    <table>
                        <thead>
                            <tr>
                                <td class="text-left py-0"><strong>Bàn:</strong> @{{ table_name }}</td>
                                <td class="text-right py-0">@{{ stt }}</td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="order-info">
                    <table>
                        <thead>
                            <tr>
                                <td class="text-left"><strong>@{{ item_name }}</strong></td>
                                <td class="text-right">@{{money item_price }}</td>
                            </tr>
                        </thead>
                    </table>
                </div>
                <span class="mark">@goido.net</span>
            </div>
        </section>

    </body>

</html>
