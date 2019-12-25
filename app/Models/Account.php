<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Carbon\Carbon;

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


    public function updateInventoryOrdersStats() {
        $inventoryOrdersStats = $this->hasOne('App\Models\InventoryOrder', 'supplier_id', 'id')
            ->selectRaw("
                SUM(if(type=0,amount,0)) as total_amount, 
                SUM(if(type=1,amount,0)) as total_return_amount, 
                SUM(debt) as total_debt")
            ->where('status', 1)
            ->whereNotNull('deleted_at')->first();

        $this->total_amount = $inventoryOrdersStats->total_amount;
        $this->total_return_amount = $inventoryOrdersStats->total_return_amount;
        $this->total_debt = $inventoryOrdersStats->total_debt;
        $this->last_order_at = Carbon::now();

        // save self acount
        $this->save();

        return $this;
    }


    public function updateOrdersStats() {
        $inventoryOrdersStats = $this->hasOne('App\Models\Order', 'customer_id', 'id')
            ->selectRaw("
                SUM(if(type=1,amount,0)) as total_amount, 
                SUM(if(type=0,amount,0)) as total_return_amount, 
                SUM(debt) as total_debt")
            ->where('is_paid', 1)->first();

        $this->total_amount = $inventoryOrdersStats->total_amount;
        $this->total_return_amount = $inventoryOrdersStats->total_return_amount;
        $this->total_debt = $inventoryOrdersStats->total_debt;
        $this->last_order_at = Carbon::now();

        // save self acount
        $this->save();

        return $this;
    }
}
