<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {{-- <link href="/css/print.css" rel="stylesheet"> --}}
    <style>
        html{line-height:1.15;-webkit-text-size-adjust:100%}
body{margin:0}
main{display:block}
h1{font-size:2em;margin:.67em 0}
hr{box-sizing:content-box;height:0;overflow:visible}
pre{font-family:monospace,monospace;font-size:1em}
a{background-color:transparent}
abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}
b,strong{font-weight:bolder}
code,kbd,samp{font-family:monospace,monospace;font-size:1em}
small{font-size:80%}
sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}
sub{bottom:-.25em}
sup{top:-.5em}
img{border-style:none}
button,input,optgroup,select,textarea{font-family:inherit;font-size:100%;line-height:1.15;margin:0}
button,input{overflow:visible}
button,select{text-transform:none}
button,[type="button"],[type="reset"],[type="submit"]{-webkit-appearance:button}
button::-moz-focus-inner,[type="button"]::-moz-focus-inner,[type="reset"]::-moz-focus-inner,[type="submit"]::-moz-focus-inner{border-style:none;padding:0}
button:-moz-focusring,[type="button"]:-moz-focusring,[type="reset"]:-moz-focusring,[type="submit"]:-moz-focusring{outline:1px dotted ButtonText}
fieldset{padding:.35em .75em .625em}
legend{box-sizing:border-box;color:inherit;display:table;max-width:100%;padding:0;white-space:normal}
progress{vertical-align:baseline}
textarea{overflow:auto}
[type="checkbox"],[type="radio"]{box-sizing:border-box;padding:0}
[type="number"]::-webkit-inner-spin-button,[type="number"]::-webkit-outer-spin-button{height:auto}
[type="search"]{-webkit-appearance:textfield;outline-offset:-2px}
[type="search"]::-webkit-search-decoration{-webkit-appearance:none}
::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}
details{display:block}
summary{display:list-item}
template{display:none}
[hidden]{display:none}
@page{margin:0}
body{margin:0}
.sheet{margin:0;overflow:hidden;position:relative;box-sizing:border-box;page-break-after:always}
body.A3 .sheet{width:297mm;height:419mm}
body.A3.landscape .sheet{width:420mm;height:296mm}
body.A4 .sheet{width:210mm;height:296mm}
body.A4.landscape .sheet{width:297mm;height:209mm}
body.A5 .sheet{width:148mm;height:209mm}
body.A5.landscape .sheet{width:210mm;height:147mm}
body.letter .sheet{width:216mm;height:279mm}
body.letter.landscape .sheet{width:280mm;height:215mm}
body.legal .sheet{width:216mm;height:356mm}
body.legal.landscape .sheet{width:357mm;height:215mm}
.sheet.padding-2mm{padding:2mm}
.sheet.padding-5mm{padding:5mm}
.sheet.padding-10mm{padding:10mm}
.sheet.padding-15mm{padding:15mm}
.sheet.padding-20mm{padding:20mm}
.sheet.padding-25mm{padding:25mm}
@media screen {
body{background:#e0e0e0}
.sheet{background:#fff;box-shadow:0 .5mm 2mm rgba(0,0,0,.3);margin:0}
}
@media print {
body.A3.landscape{width:420mm}
body.A3,body.A4.landscape{width:297mm}
body.A4,body.A5.landscape{width:210mm}
body.A5{width:148mm}
body.letter,body.legal{width:216mm}
body.letter.landscape{width:280mm}
body.legal.landscape{width:357mm}
}
body{font-family:Tahoma,sans-serif;font-size:10px;color:#000;background:#fff}
hr{border-style:dotted;border-width:0;border-bottom-width:1px}
table{width:100%;border-spacing:0}
th,td{padding:6px 0 4px}
th{color:#333;border-bottom:1px solid #ddd}
.quantity{font-size:14px}
.print{padding-bottom:5px}
.print-logo{text-align:center}
.print-logo img{height:40px}
.print-header,.order-info{margin-top:10px}
.note{padding-top:0}
.total{font-weight:700;font-size:1.2em}
.total td{padding:10px 0}
.text-left{text-align:left}
.text-right{text-align:right}
.text-center{text-align:center}
.p-0 {padding:0}
.py-0{padding-top:0;padding-bottom:0}
.py-1{padding-top:.25rem;padding-bottom:.25rem}
.pb-1{padding-bottom:.25rem}
.pt-1{padding-top:.25rem}
.my-0{margin-top:0;margin-bottom:0}
.my-1{margin-top:.25rem;margin-bottom:.25rem}
.my-2{margin-top:.5rem;margin-bottom:.5rem}
.my-3{margin-top:1rem;margin-bottom:1rem}
.mt-3{margin-top:1rem}
.mb-3{margin-bottom:1rem}
.nowrap{width:1px;padding-left:10px;white-space:nowrap}
.mark{position:absolute;right:0;bottom:0;z-index:1;font-size:8px;padding:3px}
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
                            <td class="text-left" colspan="4"><strong>@{{@index}}.</strong> @{{ name }}</td>
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
