<?php
namespace App\Traits;
use App\Models\Category;
use App\Models\Voucher;
use Carbon\Carbon;

trait HasVoucher {
	protected $exceptAttributes = [
		'updated_at',
		'created_at',
		'place',
	];
	// 1: thu
	// 0: chi
	public function createVoucher($payment_method = 'cash') {
		$voucher = new Voucher;

		$voucherData = [
			'uuid' => nanoId(),
			'place_id' => $this->place_id,
			'type' => $this->type,
			'code' => null,
			'amount' => $this->paid,
			'imported_at' => Carbon::now(),
			'state' => 1,
			'payment_method' => $payment_method,
			'creator_id' => $this->creator_id,
		];

		if (static::getTable() == 'inventory_orders') {
			$voucherData['inventory_order_id'] = $this->id;
			$voucherData['payer_payee_id'] = $this->supplier_id;
			$voucherData['category_id'] = $this->type ? 29 : 20;
			// Chi mua hàng : Thu xuất trả

		} elseif (static::getTable() == 'orders') {
			$voucherData['order_id'] = $this->id;
			$voucherData['payer_payee_id'] = $this->customer_id;
			$voucherData['category_id'] = $this->type ? 28 : 21;
			// Thu bán hàng : Tiền trả hàng
		}

		$category = Category::find($voucherData['category_id']);
		$voucherData['title'] = $this->code . ': ' . $category->name;

		// create voucher
		return Voucher::create($voucherData);

	}
}