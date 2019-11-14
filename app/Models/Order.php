<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $codePrefix = [
        0 => 'DX', // Đơn xuất
        1 => 'DN', // Đơn nhập
        2 => 'DKT', // Đơn khác trả hàng
        3 => 'DTN', // Đơn trả nhà cung cấp
    ];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'creator_id',
        'payer_payee_id',
        'user_id',
        'created_at',
    ];

    protected $guarded = ['id'];

    // ======================= Overrided ================= //

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ======================= Mutators ================= //
    public function setCodeAttribute($type, $value)
    {
        $this->attributes['code'] = is_null($value)
            ? $this->codePrefix[$type] . str_pad(static::count() + 1, 6, "0", STR_PAD_LEFT)
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

}
