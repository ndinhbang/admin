<?php

namespace App\Models;

use App\Scopes\PlaceScope;
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
        'place_id',
        'area_id',
    ];

    protected $casts = [
        'uuid'        => 'string',
        'area_id'    => 'integer',
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

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'table_id');
    }
}
