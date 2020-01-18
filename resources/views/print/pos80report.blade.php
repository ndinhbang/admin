<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/css/print.css?v=2" rel="stylesheet">
</head>

<body class="receipt w74mm">
    <section class="sheet padding-5mm">
        <div class="print">
            {{-- <div class="print-logo">
                <img src="/images/logo.svg">
            </div> --}}

            <div class="print-header">
                @if (!is_null($print_info))
                    <h1 class="text-center mb-0"><strong>{{ $print_info['title'] }}</strong></h1>
                    @if($print_info['address'])
                        <p class="my-1 text-center">{{ $print_info['address'] }}</p>
                    @endif
                    @if($print_info['phone'])
                        <p class="my-1 text-center">{{ $print_info['phone'] }}</p>
                    @endif
                @endif
                <br />
                <h1 class="text-center my-1 mt-3 mb-3"><strong>BÁO CÁO DOANH THU</strong></h1>

                <p class="text-center my-1 fs12"><strong>Từ ngày:</strong>
                    <span id="time">{{ Carbon\Carbon::parse($start_date)->format('d/m/Y H:i:s A') }}</span>
                </p>
                <p class="text-center my-1 mb-3 fs12"><strong>Đến ngày:</strong>
                    <span id="time">{{ Carbon\Carbon::parse($end_date)->format('d/m/Y H:i:s A') }}</span>
                </p>

                <div class="order-info">
                    <table>
                        <tbody>
                            <tr>
                                <td class="text-left top-border pb-2 pt-2 fs16"><strong>Số đơn đã bán</strong></td>
                                <td class="text-right top-border pb-2 pt-2 fs16"><strong>{{ $orderStats->total_order }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-left top-border pb-2 pt-2 fs12"><strong>Số lượng hàng đã bán</strong></td>
                                <td class="text-right top-border pb-2 pt-2 fs12"><strong>{{ $orderStats->total_dish }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-left top-border pb-2 pt-2 fs14"><strong>Tiền hàng</strong></td>
                                <td class="text-right top-border pb-2 pt-2 fs14"><strong>{{ number_format($orderStats->total_amount + $orderStats->total_discount_amount + $orderStats->total_discount_items_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14">Giảm giá theo đơn</td>
                                <td class="text-right pb-2 pt-2 fs14"><small>({{ $orderStats->total_discount }})</small> {{ number_format($orderStats->total_discount_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14">Giảm giá theo SP</td>
                                <td class="text-right pb-2 pt-2 fs14"><small>({{ $orderStats->total_discount_items }})</small> {{ number_format($orderStats->total_discount_items_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14"><strong>Doanh số</strong></td>
                                <td class="text-right pb-2 pt-2 fs14"><strong>{{ number_format($orderStats->total_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14">-- Mang về</td>
                                <td class="text-right pb-2 pt-2 fs14"><small>({{ round(($orderStats->takeaway_amount/$orderStats->total_amount)*100) }}%)</small> {{ number_format($orderStats->takeaway_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14">-- Tại chỗ</td>
                                <td class="text-right pb-2 pt-2 fs14"><small>({{ round(($orderStats->inplace_amount/$orderStats->total_amount)*100) }}%)</small> {{ number_format($orderStats->inplace_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14">Nợ</td>
                                <td class="text-right pb-2 pt-2 fs14"><small>({{ $orderStats->total_debt }})</small> {{ number_format($orderStats->total_debt_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14"><strong>Thực thu</strong></td>
                                <td class="text-right pb-2 pt-2 fs14"><strong>{{ number_format($orderStats->total_amount - $orderStats->total_debt_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14 top-border" colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="text-center my-1 mt-3 mb-3 fs16"><strong>THEO DANH MỤC</strong></h3>

                <div class="order-info">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-left">Tên</th>
                                <th class="text-left">SL</th>
                                <th class="text-right">Thực thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoryStats as $key => $category)
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14">{{ $category->name }}</td>
                                <td class="text-left pb-2 pt-2 fs14">{{ $category->total_quantity }}</td>
                                <td class="text-right pb-2 pt-2 fs14">
                                    {{ number_format($category->total_amount-$category->total_discount_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14 top-border" colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <br />
                <h3 class="text-center my-1 mt-3 mb-3 fs16"><strong>THEO NGƯỜI BÁN</strong></h3>

                <div class="order-info">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-left">Tên</th>
                                <th class="text-left">SL</th>
                                <th class="text-right">Thực thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userStats as $key => $user)
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14">{{ $user->display_name }}</td>
                                <td class="text-left pb-2 pt-2 fs14">{{ $user->total_quantity }}</td>
                                <td class="text-right pb-2 pt-2 fs14">{{ number_format($user->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14" colspan="3"></td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14" colspan="3"></td>
                            </tr>
                            <tr>
                                <td class="text-left pb-2 pt-2 fs14" colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <span class="mark">@Goido.NET</span>
        </div>
    </section>

</body>

</html>
