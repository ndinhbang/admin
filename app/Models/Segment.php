<?php

namespace App\Models;

use App\Http\Filters\SegmentFilter;
use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string uuid
 * @property string place_id
 * @property string title
 * @property string description
 * @method  static Segment filter( SegmentFilter $param )
 */
class Segment extends Model
{
    use Filterable;

    protected $fillable = [
        'name',
        'desc',
        'conditions',
    ];

    protected $primaryKey = 'id';

    protected $hidden = [
        'id',
        'place_id',
    ];

    protected $casts = [
        'uuid'       => 'string',
        'place_id'   => 'integer',
        'conditions' => 'array',
        'name'       => 'string',
        'desc'       => 'string',
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

    public function fixedCustomers()
    {
        return $this->customers()->wherePivot('is_fixed', 1);
    }

    public function customers()
    {
        return $this->belongsToMany('App\Models\Account', 'account_segment', 'segment_id', 'account_id')
            ->withTimestamps()
            ->withPivot([ 'is_fixed' ]);
    }
}
