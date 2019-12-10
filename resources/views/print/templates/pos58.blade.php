<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        html{line-height:1.15;-webkit-text-size-adjust:100%}
body{margin:0}
main{display:block}
h1{font-size:2em;margin:.67em 0}
hr{box-sizing:content-box;height:0;overflow:visible}
pre{font-family:monospace,monospace;font-size:1em}
a{background-color:transparent}
b,strong{font-weight:bolder}
small{font-size:80%}
sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}
sub{bottom:-.25em}
sup{top:-.5em}
img{border-style:none}
template{display:none}
[hidden]{display:none}
@page{margin:0}
body{margin:0}
.sheet{margin:0;overflow:hidden;position:relative;box-sizing:border-box;page-break-after:always}
.sheet.padding-2mm{padding:2mm}
.sheet.padding-5mm{padding:5mm}

@media screen {
body{background:#e0e0e0}
.sheet{background:#fff;box-shadow:0 .5mm 2mm rgba(0,0,0,.3);margin:0}
}
body{font-family:Tahoma,sans-serif;font-size:10px;color:#000;background:#fff}
hr{border-style:dotted;border-width:0;border-bottom-width:1px}
table{width:100%;border-spacing:0}
th,td{padding:0}
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

<body class="receipt w50mm">
        <section class="sheet p-0">
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
                                {{-- @{{#item}} --}}
                                <td class="text-left "><strong>@{{ item_name }}</strong></td>
                                {{-- @{{/item}} --}}
                            </tr>
                        </thead>
                    </table>
                </div>
                <span class="mark">@goido.net</span>
            </div>
        </section>

    </body>

</html>
