<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $table =  'supplies';

    protected $guarded = ['id'];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'created_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
//    protected $appends = ['quantity'];

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
//    public function getQuantityAttribute()
//    {
//        return $this->pivot->quantity ?? null;
//    }
}
