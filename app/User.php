<?php

namespace App;

use App\Scopes\PlaceM2MScope;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'email',
        'password',
        'display_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        if(!is_null(request()->user()) && !request()->user()->hasAnyRole(['admin', 'superadmin'])) {
            static::addGlobalScope(new PlaceM2MScope);
        }
    }

    
    public static function findUuid($uuid)
    {
        if($uuid)
            return User::where('uuid', $uuid)->first();

        return null;
    }

    /**
     * The roles that belong to the user.
     */
    // public function places()
    // {
    //     return $this->belongsToMany('App\Models\Place');
    // }

    /**
     * The roles that belong to the user.
     */
    public function places()
    {
        return $this->belongsToMany('App\Models\Place');
    }

    // change login way form `username` -> `phone`
    public function findForPassport($identifier)
    {
        return $this->orWhere('name', $identifier)->orWhere('email', $identifier)->orWhere('phone', $identifier)
            ->first();
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
