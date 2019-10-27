<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'title',
        'code',
        'address',
        'status',
        'expired_date'
    ];
    protected $primaryKey = 'id';
    protected $table = 'places';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    // public function printers()
    // {
    //     return $this->hasMany('App\Printer');
    // }

    /**
     * The users that belong to the role.
     */
    // public function roles()
    // {
    //     return $this->hasMany('Spatie\Permission\Models\Role');
    // }

}
