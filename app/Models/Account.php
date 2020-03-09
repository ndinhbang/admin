<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use Filterable;
    protected $fillable = [
        'name',
    ];
    protected $primaryKey = 'id';
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'place_id',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    public static function findUuid($uuid)
    {
        if ( $uuid ) {
            return Account::where('uuid', $uuid)->first();
        }
        return null;
    }

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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'customer_id');
    }

    public function inventoryOrders()
    {
        return $this->hasMany('App\Models\InventoryOrder', 'supplier_id');
    }

    public function updateInventoryOrdersStats()
    {
        // todo: refact needed
        $inventoryOrdersStats             = $this->hasOne('App\Models\InventoryOrder', 'supplier_id', 'id')
            ->selectRaw(
                "
                SUM(if(type=0,amount,0)) as total_amount, 
                SUM(if(type=1,amount,0)) as total_return_amount, 
                SUM(debt) as total_debt"
            )
            ->where('status', 1)->first();
        $this[ 'stats->amount' ]          = $inventoryOrdersStats->total_amount;
        $this[ 'stats->returned_amount' ] = $inventoryOrdersStats->total_return_amount;
        $this[ 'stats->debt' ]            = $inventoryOrdersStats->total_debt;
        $this[ 'stats->last_order_at' ]   = Carbon::now()->format('Y-m-d H:i:s');
        // save self acount
        $this->save();
        return $this;
    }

    public function updateOrdersStats()
    {
        // todo: refact needed
        $orderStats                       = $this->hasOne('App\Models\Order', 'customer_id', 'id')
            ->selectRaw(
                "
                COUNT(*) as order_count,
                SUM(if(type=1,amount,0)) as total_amount, 
                SUM(if(type=0,amount,0)) as total_return_amount, 
                SUM(debt) as total_debt"
            )
            ->where('is_paid', 1)->first();
        $this[ 'stats->amount' ]          = $orderStats->total_amount;
        $this[ 'stats->returned_amount' ] = $orderStats->total_return_amount;
        $this[ 'stats->debt' ]            = $orderStats->total_debt;
        $this[ 'stats->last_order_at' ]   = Carbon::now()->format('Y-m-d H:i:s');
        $this[ 'stats->order_count' ]     = $orderStats->order_count;
        // save self acount
        $this->save();
        return $this;
    }
}
