<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PromotionDetail extends Pivot
{
    public $incrementing = true;

    protected $table = 'promotion_detail';

    protected $guarded = [ 'id' ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
