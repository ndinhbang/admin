<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    public $incrementing = true;
    protected $table = 'order_items';
    protected $guarded = ['id'];

    protected $hidden = [
        'id',
        'order_id',
        'product_id',
    ];

//    protected static function boot()
//    {
//        parent::boot();
//
//        static::addGlobalScope(new PlaceScope);
//    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
