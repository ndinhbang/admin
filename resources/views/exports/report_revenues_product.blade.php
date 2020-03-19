<table>
    <tbody>
        <tr>
            <td colspan="9" align="center" style="font-size: 20px; padding-bottom: 20px; height: 60px; line-height: 60px;"><strong>BÁO CÁO DOANH SỐ THEO SẢN PHẨM</strong></td>
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
                Tiền hàng<br />
                <strong style="font-size: 18px">{{ $stats->total_amount + $stats->total_discount_amount + $stats->total_discount_items_amount }}</strong>
            </td>
            <td>
                Giảm giá <small>(Đơn + SP)</small><br />
                <strong style="font-size: 18px">{{ $stats->total_discount_amount + $stats->total_discount_order_amount }}</strong>
            </td>
            <td>
                Doanh số<br />
                <strong style="font-size: 18px">{{ $stats->total_amount }}</strong>
            </td>
            <td>
                Số lượng bán<br />
                <strong style="font-size: 18px">{{ $stats->total_quantity }}</strong>
            </td>
            <td colspan="2">
            </td>
        </tr>
        <tr>
            <td colspan="9"></td>
        </tr>
        <tr>
            <td style="background: #eeeeee; line-height: 36px"><strong>Mã SP</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Tên SP</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Số lượng</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Tiền hàng</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Giảm giá</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Doanh số</strong></td>
        </tr>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->code }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->total_quantity }}</td>
                <td>{{ $item->total_amount }}</td>
                <td>{{ $item->total_discount_amount + $item->total_discount_order_amount }}</td>
                <td>{{ $item->total_amount - $item->total_discount_order_amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>