<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PlaceScope;
use App\Traits\Filterable;

class Account extends Model
{
    use Filterable;
    protected $fillable = [
        'name'
    ];
    protected $primaryKey = 'id';
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
            return Account::where('uuid', $uuid)->first();

        return null;
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
