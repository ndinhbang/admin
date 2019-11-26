<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
	use Filterable;

	protected $table = 'products';
	protected $codePrefix = 'P';

	// ======================= Hidden Attributes ================= //
	protected $hidden = [
		'id',
		'place_id',
		'category_id',
		'created_at',
	];

	protected $guarded = ['id'];

	// ======================= Attribute Casting ================= //
	protected $casts = [
		'uuid' => 'string',
		'place_id' => 'integer',
		'category_id' => 'integer',
		'position' => 'integer',
		'state' => 'boolean',
		'is_hot' => 'boolean',
		'price' => 'double',
		'price_sale' => 'double',
		'opened' => 'boolean',
		'can_stock' => 'boolean',
		'thumbnail' => 'string',
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

	// ======================= Accessors ================= //
	// ======================= Mutators ================= //
	public function setCodeAttribute($value) {
		$codeId = 0;
		if (!is_null($row = static::select('code')
			->orderBy('id', 'desc')
			->take(1)
			->first())) {
			$codeId = (int) str_replace($this->codePrefix, '', $row->code);
		}
		$this->attributes['code'] = is_null($value) ? $this->codePrefix . str_pad($codeId + 1, 6, "0",
			STR_PAD_LEFT) : $value;
	}

	// ======================= Local Scopes ================= //
	public function scopeActive($query) {
		return $query->where('state', 1);
	}

	// ======================= Relationships ================= //
	public function place() {
		return $this->belongsTo('App\Models\Place', 'place_id');
	}

	public function category() {
		return $this->belongsTo('App\Models\Category', 'category_id');
	}

	public function orders() {
		return $this->belongsToMany('App\Models\Order', 'order_items', 'product_id', 'order_id');
	}

	public function supplies() {
		return $this->belongsToMany('App\Models\Supply', 'product_supply', 'product_id', 'supply_id')
			->withPivot('quantity')
			->with('stocks');
	}
}
