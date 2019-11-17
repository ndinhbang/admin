<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryOrder extends Model
{
    use SoftDeletes;
    
    protected $table = 'inventory_orders';
    protected $codePrefix = [
        1 => 'DN', // Đơn nhập
        0 => 'DTN', // Đơn trả nhà cung cấp
    ];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'creator_id',
        'user_id',
        'supplier_id',
        'created_at',
    ];

    protected $guarded = ['id'];


    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'          => 'string',
        'place_id'      => 'integer',
        'supplier_id'   => 'integer',
        'creator_id'    => 'integer',
        'user_id'       => 'integer',
        'amount'        => 'double',
        'debt'          => 'double',
        'paid'          => 'double',
        'attached_files'=> 'string',
        'note'          => 'string',
        'type'          => 'integer',
    ];

    // ======================= Overrided ================= //

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ======================= Mutators ================= //

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = is_null($value)
            ? $this->codePrefix[$this->type] . str_pad(static::count() + 1, 6, "0", STR_PAD_LEFT)
            : $value;
    }

    // ======================= Relationships ================= //
    public function place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Account', 'supplier_id');
    }

    public function supplies()
    {
        return $this->belongsToMany('App\Models\Supply', 'inventory', 'inventory_order_id', 'supply_id')
            ->withPivot('quantity', 'total_price', 'price_pu')
            ->withTimestamps();
    }
}
