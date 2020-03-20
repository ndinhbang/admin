<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        <?php include( public_path() . '/css/print.css' ); ?>
    </style>
</head>
<body class="receipt w50mm">
<section class="sheet padding-5mm">
    <div class="print">
        <div class="print-header my-0 mb-0">
            <div class="pb-1 clearfix">
                <div class="w50 float-left">
                    @{{#if created_at}}
                    <small class="no-wrap">@{{ created_at }}</small>
                    @{{/if}}
                </div>
                <div class="w50 float-right text-right">
                    @{{#if order_code}}
                    <small>@{{ order_code }}</small>
                    @{{/if}}
                </div>
            </div>
            <table>
                <thead>
                <tr>
                    <td class="text-left py-0" width="93%">
                        <div class="no-wrap"><strong>@{{ card_name }}</strong> | @{{ area_name }}-@{{ table_name }}
                        </div>
                    </td>
                    <td class="text-right py-0"><strong class="no-wrap">@{{ stt }}</strong></td>
                </tr>
                </thead>
            </table>
        </div>
        <div class="order-info">
            <table>
                <thead>
                <tr>
                    <td class="text-left py-0" width="90%"><strong>@{{ item_name }}</strong></td>
                    <td class="text-right py-0">
                        <div class="no-wrap">@{{money item_price }}</div>
                    </td>
                </tr>
                @{{#if children}}
                <tr>
                    <td class="text-left py-0" colspan="2">
                        <small>
                            @{{#each children}}
                            - @{{ product_name }};
                            @{{/each}}
                        </small>
                    </td>
                </tr>
                @{{/if}}
                @{{#if item_note}}
                <tr>
                    <td class="text-left" colspan="2">
                        <div class="no-wrap"><em>@{{ item_note }}</em></div>
                    </td>
                </tr>
                @{{/if}}
                </thead>
            </table>
        </div>
    </div>
</section>
</body>
</html>
