<?php

namespace App\Models;

use App\Http\Filters\SegmentFilter;
use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property string uuid
 * @property string place_id
 * @property string title
 * @property string description
 * @method  static Segment filter(SegmentFilter $param)
 */
class Segment extends Eloquent
{
    use Filterable;

    protected $with = ['customers', 'criteria'];

    protected $fillable = [
        'title',
        'description',
        'uuid',
        'place_id',
    ];

    protected $primaryKey = 'id';

    protected $hidden = [
        'id',
        'place_id',
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


    /**
     * @return BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(Account::class, 'segments_accounts')
            ->where('type', '=', 'customer')->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function criteria()
    {
        return $this->hasMany(Criterion::class);
    }
}
