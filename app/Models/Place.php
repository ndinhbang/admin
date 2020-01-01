<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'uuid',
        'title',
        'code',
        'address',
        'status',
        'expired_date',
        'contact_name',
        'contact_phone',
        'contact_email',
        'user_id',
        'config_sale',
        'config_print',
        'config_screen2nd',
    ];
    protected $primaryKey = 'id';
    protected $table = 'places';

    protected $casts = [
        'user_id'          => 'integer',
        'print_templates'  => 'array',
        'config_sale'      => 'array',
        'config_print'     => 'array',
        'config_screen2nd' => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'id',
        'user_id',
        'pivot',
    ];

    public static function findUuid( $uuid )
    {
        if ( $uuid ) {
            return Place::where('uuid', $uuid)
                ->first();
        }
        return null;
    }

    /**
     * The owner of place
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User')
            ->with('roles');
    }

    /**
     * Users of place
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'place_id');
    }

    public function areas()
    {
        return $this->hasMany('App\Models\Area', 'place_id');
    }

    public function tables()
    {
        return $this->hasMany('App\Models\Table', 'place_id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'place_id');
    }

    public function categories()
    {
        return $this->hasMany('App\Models\Category', 'place_id');
    }

    /**
     * Get the route key for the model.
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
