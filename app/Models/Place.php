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
    ];
    protected $primaryKey = 'id';
    protected $table = 'places';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    public function scopeCurr($query)
    {
        if (!is_null($placeId = request()->header('X-Place-Id'))) {
            return $query->where('uuid', $placeId)->first();
        }
        
        return response()->json(['errors' => ['' => ['Không tìm thấy thông tin cửa hàng!']]], 422);
    }

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