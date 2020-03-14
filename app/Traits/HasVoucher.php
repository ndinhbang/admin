<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Voucher;
use Carbon\Carbon;

trait HasVoucher
{
    protected $exceptAttributes = [
        'updated_at',
        'created_at',
        'place',
    ];
    // 1: thu
    // 0: chi
    public function createVoucher($payment_method = 'cash', $creator_id = null, $type = null, $message = null)
    {
        $voucher = new Voucher;
        $voucherData = [
            'uuid'           => nanoId(),
            'place_id'       => $this->place_id,
            'type'           => is_null($type) ? $this->type : $type,
            'code'           => null,
            'amount'         => $this->paid,
            'imported_at'    => Carbon::now(),
            'state'          => 1,
            'payment_method' => $payment_method,
            'creator_id'     => is_null($creator_id) ? $this->creator_id : $creator_id,
        ];

        if(!$voucherData['amount'] || $voucherData['amount'] <= 0)
            return null;
        
        if ( static::getTable() == 'inventory_orders' ) {
            $voucherData['inventory_order_id'] = $this->id;
            $voucherData['payer_payee_id']     = $this->supplier_id;
            $voucherData['category_id']        = $voucherData['type'] ? 30 : 21;

            $voucherData['note'] = is_null($message) ? '' : $message.' cho đơn nhập '.$this->code;
            // Thu xuất trả :  Chi mua hàng
        } elseif ( static::getTable() == 'orders' ) {
            $voucherData['order_id']       = $this->id;
            $voucherData['payer_payee_id'] = $this->customer_id ?? 0;
            $voucherData['category_id']    = $voucherData['type'] ? 29 : 22;
            // Thu bán hàng : Tiền trả hàng

            $voucherData['note'] = is_null($message) ? '' : $message.' cho đơn hàng '.$this->code;
        }
        $category             = Category::find($voucherData['category_id']);
        $voucherData['title'] = $this->code . ': ' . $category->name;
        // dump($voucherData);
        // create voucher
        return Voucher::create($voucherData);
    }
}
