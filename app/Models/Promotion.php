<?php

namespace App\Models;

use App\Model;
use App\Scopes\PlaceScope;
use App\Traits\Filterable;

class Promotion extends Model
{
    use Filterable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'note',
        'type',
        'state',
        'from',
        'to',
        'is_limited',
        'limit_qty',
        'remain_qty',
        'applied',
        'required_code',
        'total',
        'rule',
        'customers',
        'segments',
    ];
    protected $hidden = [
        'id',
        'place_id',
    ];

    protected $casts = [
        'uuid'          => 'string',
        'place_id'      => 'integer',
        'name'          => 'string',
        'note'          => 'string',
        'type'          => 'string',
        'code'          => 'string',
        'state'         => 'integer',
        'from'          => 'datetime',
        'to'            => 'datetime',
        'is_limited'    => 'boolean',
        'limit_qty'     => 'integer',
        'remain_qty'    => 'integer',
        'applied'       => 'array',
        'required_code' => 'boolean',
        'total'         => 'array',
        'rule'          => 'array',
        'customers'     => 'array',
        'segments'      => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PlaceScope);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
