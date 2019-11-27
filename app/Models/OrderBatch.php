<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use Illuminate\Database\Eloquent\Model;

class OrderBatch extends Model
{
    protected $table = 'order_batchs';
    protected $guarded = ['id'];

    protected $hidden = [
        'id',
        'order_id',
        'product_id',
        'place_id',
    ];

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

    public function place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}
