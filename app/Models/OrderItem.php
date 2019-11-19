<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    public $incrementing = true;
    protected $guarded = ['id'];
}
