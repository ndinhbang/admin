<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderBatch extends Model
{
    protected $table = 'order_batchs';

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
}
