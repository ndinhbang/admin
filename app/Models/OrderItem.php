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
        'discount_id',
    ];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'                   => 'string',
        'place_id'               => 'integer',
        'parent_id'              => 'integer',
        'order_id'               => 'integer',
        'product_id'             => 'integer',
        'quantity'               => 'integer',
        'added_qty'              => 'integer',
        'total_price'            => 'integer',
        'simple_price'           => 'integer',
        'children_price'         => 'integer',
        'total_buying_price'     => 'integer',
        'total_buying_avg_price' => 'integer',
        'discount_id'            => 'integer',
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

    public function place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }

    /**
     * The order that item belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    /**
     * Produc of item, 1 item has only 1 product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

//    public function parent()
//    {
//        return $this->belongsTo('App\Models\OrderItem', 'parent_id');
//    }
    public function children()
    {
        return $this->hasMany('App\Models\OrderItem', 'parent_id');
    }
}
