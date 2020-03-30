<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Inventory extends Pivot
{
    public $incrementing = true;
    protected $table = 'inventory';

    protected $guarded = [ 'id' ];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'created_at',
    ];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'place_id'    => 'integer',
        'ref_code'    => 'string',
        'supply_id'   => 'integer',
        'total_price' => 'double',
        'price_pu'    => 'double',
        'qty_import'  => 'double',
        'qty_export'  => 'double',
        'qty_remain'  => 'double',
        'note'        => 'string',
    ];

    public function inventoryOrder()
    {
        return $this->belongsTo(InventoryOrder::class, 'inventory_order_id');
    }
}
