<?php

namespace App\Models;

use App\Traits\CustomizeEloquentBuilder;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model {
	use CustomizeEloquentBuilder;

	protected $table = 'supplies';

	protected $guarded = ['id'];

	// ======================= Hidden Attributes ================= //
	protected $hidden = [
		'id',
		'place_id',
	];

	// ======================= Attribute Casting ================= //
	protected $casts = [
		'uuid' => 'string',
		'place_id' => 'integer',
		'unit_id' => 'integer',
		'name' => 'string',
		'price_in' => 'double',
		'min_stock' => 'integer',
		'max_stock' => 'integer',
	];

	// ======================= Overrided ================= //

	/**
	 * {@inheritDoc}
	 */
	public function getRouteKeyName() {
		return 'uuid';
	}

	public function unit() {
		return $this->belongsTo('App\Models\Category', 'unit_id');
	}

	public function products() {
		return $this->belongsToMany('App\Models\Product', 'product_supply', 'supply_id', 'product_id')
			->withPivot(['quantity']);
	}

	public function inventory() {
		return $this->belongsToMany('App\Models\InventoryOrder', 'inventory', 'supply_id', 'inventory_order_id')
			->select('inventory_orders.*', 'accounts.name as supplier_name', 'users.display_name as creator_name')
			->join('accounts', 'accounts.id', '=', 'inventory_orders.supplier_id')
			->join('users', 'users.id', '=', 'inventory_orders.creator_id')
			->withPivot(['quantity', 'remain', 'price_pu', 'total_price'])
			->where('inventory_orders.status', 1)
			->withTimestamps();
	}

	public function orders() {
		return $this->belongsTo('App\Models\InventoryOrder', 'inventory', 'supply_id', 'inventory_order_id')
			->select('inventory_orders.*', 'accounts.name as supplier_name', 'users.display_name as creator_name')
			->join('accounts', 'accounts.id', '=', 'inventory_orders.supplier_id')
			->join('users', 'users.id', '=', 'inventory_orders.creator_id')
			->withPivot(['quantity', 'remain', 'price_pu', 'total_price'])
			->where('inventory_orders.status', 1)
			->withTimestamps();
	}
}
