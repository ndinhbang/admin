<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Filterable;

    protected $table = 'orders';
    protected $codePrefix = [
        0 => 'DX',  // Đơn xuất
        1 => 'DN',  // Đơn nhập
        2 => 'DKT', // Đơn khác trả hàng
        3 => 'DTN', // Đơn trả nhà cung cấp
    ];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'creator_id',
        'payer_payee_id',
    ];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'           => 'string',
        'place_id'       => 'integer',
        'creator_id'     => 'integer',
        'payer_payee_id' => 'integer',
        'state'          => 'boolean',
        'kind'           => 'integer',
        'quantity'       => 'integer',
        'on_date'        => 'date',
        'type'           => 'integer',
    ];

    /**
     * Default values for attributes
     * Note: Keep it in sync with default values that you set for filed in database
     * @var  array
     */
    protected $attributes = [
        'state'        => 0,
        'amount'       => 0,
        'debt'         => 0,
        'paid'         => 0,
        'is_returned'  => false,
        'is_canceled'  => false,
        'is_served'    => false,
        'is_paid'      => false,
        'is_completed' => false,
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
    public function setCodeAttribute($value)
    {
        $codeId = 0;
        $type = $this->type ?? 1;
        if (!is_null($row = static::select('code')->orderBy('id', 'desc')->take(1)->first())) {
            $codeId = (int)str_replace($this->codePrefix[$type], '', $row->code);
        }

        $this->attributes['code'] = is_null($value) ? $this->codePrefix[$type] . str_pad($codeId + 1, 6, "0",
                STR_PAD_LEFT) : $value;
    }

    // ======================= Local Scopes ================= //
    public function scopeProgressing($query)
    {
        return $query->where('is_returned', 0)
            ->where('is_canceled', 0)
            ->where('is_completed', 0);
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

    public function customer()
    {
        return $this->belongsTo('App\User', 'customer_id');
    }

}
