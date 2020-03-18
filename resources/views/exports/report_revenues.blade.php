<table>
    <tbody>
        <tr>
            <td colspan="9" align="center" style="font-size: 20px; padding-bottom: 20px; height: 60px; line-height: 60px;"><strong>BÁO CÁO DOANH SỐ THEO ĐƠN HÀNG</strong></td>
        </tr>
        <tr>
            <td colspan="9"></td>
        </tr>
        <tr>
            <td>Từ: </td>
            <td colspan="8">{{ $request->start }}</td>
        </tr>
        <tr>
            <td>Đến: </td>
            <td colspan="8">{{ $request->end }}</td>
        </tr>
        <tr>
            <td colspan="9"></td>
        </tr>
        <tr>
            <td>
                Số đơn<br />
                <strong style="font-size: 18px">{{ $stats->total_order }}</strong>
            </td>
            <td>
                Tiền hàng<br />
                <strong style="font-size: 18px">{{ $stats->total_amount + $stats->total_discount_amount + $stats->total_discount_items_amount }}</strong>
            </td>
            <td>
                Doanh số<br />
                <strong style="font-size: 18px">{{ $stats->total_amount }}</strong>
            </td>
            <td>
                Giảm giá <small>(Đơn + SP)</small><br />
                <strong style="font-size: 18px">{{ $stats->total_discount_amount + $stats->total_discount_items_amount }}</strong>
            </td>
            <td>
                Nợ<br />
                <strong style="font-size: 18px">{{ $stats->total_debt }}</strong>
            </td>
            <td>
                Thực thu<br />
                <strong style="font-size: 18px">{{ $stats->total_amount-$stats->total_debt_amount }}</strong>
            </td>
            <td colspan="3">
            </td>
        </tr>
        <tr>
            <td colspan="9"></td>
        </tr>
        <tr>
            <td style="background: #eeeeee; line-height: 36px"><strong>Mã đơn</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Ngày bán</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Người bán</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Số lượng</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Tiền hàng</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Giảm giá</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Doanh số</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Nợ</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Thực thu</strong></td>
        </tr>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->code }}</td>
                <td>{{ Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}</td>
                <td>{{ $item->display_name }}</td>
                <td>{{ $item->total_dish }}</td>
                <td>{{ $item->amount + $item->discount_amount + $item->discount_items_amount }}</td>
                <td>{{ $item->discount_amount + $item->discount_items_amount }}</td>
                <td>{{ $item->amount }}</td>
                <td>{{ $item->debt }}</td>
                <td>{{ $item->amount - $item->debt }}</td>
            </tr>
        @endforeach
    </tbody>
</table>