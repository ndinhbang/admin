<?php

namespace App\Models;

use App\Traits\HasVoucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GenerateCode;
use App\Traits\Filterable;
use App\Scopes\PlaceScope;

class InventoryOrder extends Model {
	use Filterable, SoftDeletes, HasVoucher, GenerateCode;

	protected $table = 'inventory_orders';
	protected $codePrefix = [
		0 => 'DN', // Đơn nhập
		1 => 'DTN', // Đơn trả nhà cung cấp
	];

	// ======================= Hidden Attributes ================= //
	protected $hidden = [
		'id',
		'place_id',
		'creator_id',
		'user_id',
		'supplier_id',
	];

	protected $guarded = ['id'];

	// ======================= Attribute Casting ================= //
	protected $casts = [
		'uuid' => 'string',
		'place_id' => 'integer',
		'supplier_id' => 'integer',
		'creator_id' => 'integer',
		'user_id' => 'integer',
		'amount' => 'double',
		'debt' => 'double',
		'paid' => 'double',
		'attached_files' => 'string',
		'payment_method' => 'string',
		'note' => 'string',
		'type' => 'integer',
		'status' => 'integer',
	];

	// ======================= Overrided ================= //

	/**
	 * {@inheritDoc}
	 */
	public function getRouteKeyName() {
		return 'uuid';
	}

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot() {
		parent::boot();

		static::addGlobalScope(new PlaceScope);
	}

	// ======================= Mutators ================= //

	public function setCodeAttribute($value) {
        $this->attributes['code'] = is_null($value) ? $this->gencode($this->codePrefix[$this->type]) : $value;
	}

	// ======================= Relationships ================= //
	public function place() {
		return $this->belongsTo('App\Models\Place', 'place_id');
	}

	public function creator() {
		return $this->belongsTo('App\User', 'creator_id');
	}

	public function supplier() {
		return $this->belongsTo('App\Models\Account', 'supplier_id');
	}

	public function supplies() {
		return $this->belongsToMany('App\Models\Supply', 'inventory', 'inventory_order_id', 'supply_id')
			->withPivot('qty_import', 'qty_export', 'qty_remain', 'total_price', 'price_pu')
			->withTimestamps();
	}

	public function vouchers() {
		return $this->hasMany('App\Models\Voucher');
	}
}
