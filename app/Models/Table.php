<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create( array $array )
 */
class Table extends Model
{
    protected $table = 'tables';

    protected $guarded = ['id'];

    protected $hidden = [
        'id',
        'area_id',
        'order_id',
    ];

    protected $casts = [
        'uuid'        => 'string',
        'area_id'    => 'integer',
    ];

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Area', 'area_id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}
