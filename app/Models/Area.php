<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create( array $array_merge )
 */
class Area extends Model
{
    use Filterable;

    protected $table = 'areas';
    protected $guarded = [ 'id' ];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'created_at',
    ];

    protected $casts = [
        'uuid'     => 'string',
        'place_id' => 'integer',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PlaceScope);
    }

    public function place()
    {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }

    public function tables()
    {
        return $this->hasMany('App\Models\Table', 'area_id');
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

}
