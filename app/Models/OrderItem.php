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
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PlaceScope);
    }

    /**
     * The order that item belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    /**
     * Produc of item, 1 item has only 1 product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\OrderItem', 'parent_id');
    }
}
