<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\PlaceScope;

/**
 * @method static find( int $category_id )
 * @property mixed     description
 * @property mixed     name
 * @property string    uuid
 * @property int|mixed parent_id
 * @property mixed     type
 * @property mixed     place_id
 * @property mixed     position
 * @property mixed     state
 */
class Category extends Model
{
    use Filterable;

    protected $fillable = [
        'position'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'place_id'
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
    
    public static function findUuid($uuid)
    {
        if($uuid)
            return Category::where('uuid', $uuid)->first();

        return null;
    }
    
    public function place()
    {
        return $this->belongsTo('App\Models\Place');
    }

    public function products() {
        return $this->hasMany('App\Models\Product', 'category_id');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
