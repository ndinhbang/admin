<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PlaceScope;

class Category extends Model
{

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
