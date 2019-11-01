<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PlaceScope;

class Voucher extends Model
{
    protected $fillable = [
        'imported_date'
    ];
    protected $primaryKey = 'id';
    protected $table = 'vouchers';

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
    
    /**
     * The roles that belong to the user.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }
    
    /**
     * The roles that belong to the category.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
    
    /**
     * Người trả tiền
     */
    public function payer_payee()
    {
        return $this->belongsTo('App\Models\Account', 'payer_payee_id');
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
