<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    public $incrementing = true;

    protected $table = 'order_items';

    protected $guarded = [ 'id' ];

    protected $hidden = [
        'id',
        'place_id',
        'order_id',
        'product_id',
        'parent_id',
        'promotion_id',
    ];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'                   => 'string',
        'place_id'               => 'integer',
        'parent_id'              => 'integer',
        'order_id'               => 'integer',
        'product_id'             => 'integer',
        'promotion_id'           => 'integer',
        'quantity'               => 'double',
        'added_qty'              => 'integer',
        'printed_qty'            => 'integer',
        'time_used'              => 'integer',
        'price_by_time'          => 'boolean',
        'product_price'          => 'integer',
        'total_price'            => 'integer',
        'simple_price'           => 'integer',
        'children_price'         => 'integer',
        'total_buying_price'     => 'integer',
        'total_buying_avg_price' => 'integer',
        'discount_amount'        => 'integer',
        'discount_order_amount'  => 'integer',
        'canceled'               => 'integer',
        'completed'              => 'integer',
        'delivering'             => 'integer',
        'done'                   => 'integer',
        'doing'                  => 'integer',
        'accepted'               => 'integer',
        'pending'                => 'integer',
        'note'                   => 'string',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PlaceScope);
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\OrderItem', 'parent_id');
    }
}
