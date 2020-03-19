<table>
    <tbody>
        <tr>
            <td colspan="9" align="center" style="font-size: 20px; padding-bottom: 20px; height: 60px; line-height: 60px;"><strong>BÁO CÁO DOANH SỐ THEO NHÂN VIÊN</strong></td>
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
            <td style="background: #eeeeee; line-height: 36px"><strong>Nhân viên</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Số lượng SP</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Số lượng đơn</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Tiền hàng</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Giảm giá</strong></td>
            <td style="background: #eeeeee; line-height: 36px"><strong>Doanh số</strong></td>
        </tr>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->display_name }}</td>
                <td>{{ $item->total_quantity }}</td>
                <td>{{ $item->total_order }}</td>
                <td>{{ $item->total_amount + $item->total_discount_amount + $item->total_discount_items_amount }}</td>
                <td>{{ $item->total_discount_amount + $item->total_discount_items_amount }}</td>
                <td>{{ $item->total_amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>