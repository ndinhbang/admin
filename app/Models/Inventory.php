<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model {
	protected $table = 'inventory';

	protected $guarded = ['id'];

	// ======================= Hidden Attributes ================= //
	protected $hidden = [
		'id',
		'place_id',
		'created_at',
	];
}
