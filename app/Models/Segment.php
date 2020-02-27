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
        'customers',
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
        'customers'  => 'array',
        'conditions' => 'array',
        'name'       => 'integer',
        'desc'       => 'string',
    ];

    /**
     * Default values for attributes
     * Note: Keep it in sync with default values that you set for filled in database
     *
     * @var  array
     */
    protected $attributes = [
        'customers'  => [],
        'conditions' => [],
        'desc'       => '',
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
