<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GenerateCode;
use App\Traits\Filterable;
use App\Scopes\PlaceScope;

class InventoryTake extends Model
{
    use Filterable, GenerateCode;

    protected $table = 'inventory_takes';
    protected $codePrefix = 'PKK';

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'creator_id',
        'user_id',
    ];

    protected $guarded = ['id'];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid' => 'string',
        'place_id' => 'integer',
        'creator_id' => 'integer',
        'user_id' => 'integer',
        'qty' => 'double',
        'qty_diff' => 'double',
        'qty_excessing' => 'double',
        'qty_missing' => 'double',
        'note' => 'string',
        'status' => 'integer',
    ];

    // ======================= Overrided ================= //

    /**
     * {@inheritDoc}
     */
    public function getRouteKeyName() {
        return 'uuid';
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot() {
        parent::boot();

        static::addGlobalScope(new PlaceScope);
    }

    // ======================= Mutators ================= //

    public function setCodeAttribute($value) {
        $this->attributes['code'] = is_null($value) ? $this->gencode($this->codePrefix) : $value;
    }

    // ======================= Relationships ================= //
    public function place() {
        return $this->belongsTo('App\Models\Place', 'place_id');
    }

    public function creator() {
        return $this->belongsTo('App\User', 'creator_id');
    }

    public function supplies() {
        return $this->belongsToMany('App\Models\Supply', 'inventory', 'inventory_order_id', 'supply_id')
            ->withPivot('qty_import', 'qty_export', 'qty_remain', 'total_price', 'price_pu')
            ->withTimestamps();
    }
}
