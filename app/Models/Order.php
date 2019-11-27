<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasVoucher;

/**
 * @property float|int    amount
 * @property int          total_dish
 * @property mixed        id
 * @property int          is_canceled
 * @property mixed|string reason
 * @property int|mixed    total_eater
 * @property mixed|string note
 * @property int|mixed    customer_id
 * @property int|mixed    paid
 * @property int|mixed    debt
 * @property bool         is_paid
 * @property int|mixed    received_amount
 * @property mixed        is_returned
 * @property mixed        is_completed
 * @property mixed|string card_name
 * @property mixed        place_id
 * @property  integer|null table_id
 * @method static select( string $string )
 * @method static create( array $array_merge )
 */
class Order extends Model
{
    use Filterable, HasVoucher;

    protected $table = 'orders';
    protected $codePrefix = 'HD';

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'creator_id',
        'customer_id',
        'table_id',
    ];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'            => 'string',
        'place_id'        => 'integer',
        'creator_id'      => 'integer',
        'customer_id'     => 'integer',
        'table_id'        => 'integer',
        'state'           => 'integer',
        'kind'            => 'integer',
        'total_dish'      => 'integer',
        'total_eater'     => 'integer',
        'note'            => 'string',
        'reason'          => 'string',
        'received_amount' => 'integer',
        'debt'            => 'integer',
        'paid'            => 'integer',
        'amount'          => 'integer',
        'day'             => 'integer',
        'month'           => 'integer',
        'year'            => 'integer',
        'is_returned'     => 'boolean',
        'is_canceled'     => 'boolean',
        'is_served'       => 'boolean',
        'is_paid'         => 'boolean',
        'is_completed'    => 'boolean',
        'card_name'       => 'string',
    ];

    /**
     * Default values for attributes
     * Note: Keep it in sync with default values that you set for filed in database
     *
     * @var  array
     */
    protected $attributes = [
        'received_amount' => 0,
        'state'           => 0,
        'amount'          => 0,
        'debt'            => 0,
        'paid'            => 0,
        'total_dish'      => 0,
        'total_eater'     => 0,
        'is_returned'     => false,
        'is_canceled'     => false,
        'is_served'       => false,
        'is_paid'         => false,
        'is_completed'    => false,
    ];

    protected $guarded = [ 'id' ];

    // ======================= Overrided ================= //

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new PlaceScope);
    }

    // ======================= Mutators ================= //
    public function setCodeAttribute($value)
    {
        $codeId = 0;
        if ( !is_null($row = static::select('code')
            ->orderBy('id', 'desc')
            ->take(1)
            ->first()) ) {
            $codeId = (int) str_replace($this->codePrefix, '', $row->code);
        }
        $this->attributes['code'] = is_null($value) ? $this->codePrefix . str_pad($codeId + 1, 6, "0",
                STR_PAD_LEFT) : $value;
    }

    // ======================= Local Scopes ================= //
    public function scopeProgressing($query)
    {
        return $query->where('is_returned', 0)
            ->where('is_canceled', 0)
            ->where('is_served', 0)
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
        return $this->belongsTo('App\Models\Account', 'customer_id');
    }

    public function table()
    {
        return $this->belongsTo('App\Models\Table', 'table_id');
    }

    public function batchs()
    {
        return $this->hasMany('App\Models\OrderBatch', 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItem', 'order_id');
    }

    public function items()
    {
        return $this->products()
            ->withPivot([
                'id',
                'quantity',
                'total_price',
                'note',
            ]);
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'order_items')
//            ->using('App\Models\OrderItem')
            ->withTimestamps();
    }

}