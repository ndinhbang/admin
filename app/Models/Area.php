<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use Filterable;

    protected $table = 'areas';
    protected $guarded = ['id'];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'created_at',
    ];

    protected $casts = [
        'uuid'        => 'string',
        'place_id'    => 'integer',
    ];

    public function tables()
    {
        return $this->hasMany('App\Models\Table');
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

}
