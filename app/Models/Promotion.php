<?php

namespace App\Models;

use App\Model;
use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Promotion extends Model
{
    use Filterable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'note',
        'type',
        'state',
        'from',
        'to',
        'is_limited',
        'limit_qty',
        'applied',
        'required_code',
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
//        'from'          => 'datetime',
//        'to'            => 'datetime',
        'is_limited'    => 'boolean',
        'limit_qty'     => 'integer',
        'applied'       => 'array',
        'required_code' => 'boolean',
        'stats'         => 'array',
        'rule'          => 'array',
        'customers'     => 'array',
        'segments'      => 'array',
    ];

    protected $dates = [
        'from',
        'to',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PlaceScope);
    }

    public function setFromAttribute($date)
    {
        $this->attributes[ 'from' ] = Carbon::parse($date);
    }

    public function setToAttribute($date)
    {
        $this->attributes[ 'to' ] = $date ? Carbon::parse($date) : null;
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function scopeActive($query): Builder
    {
        return $query->where('state', 1);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'promotion_detail', 'promotion_id', 'order_id')
            ->withPivot([ 'discount_amount' ])
            ->withTimestamps();
    }
}
