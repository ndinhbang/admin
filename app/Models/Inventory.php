<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Inventory extends Pivot
{
    public $incrementing = true;
    protected $table = 'inventory';

    protected $guarded = [ 'id' ];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'created_at',
    ];
}
