<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use App\Traits\GenerateCode;
use App\Traits\HasVoucher;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryOrder extends Model
{
    use Filterable, SoftDeletes, HasVoucher, GenerateCode;

    protected $table = 'inventory_orders';
    protected $codePrefix = [
        0 => 'DN',  // Đơn nhập
        1 => 'DTN', // Đơn trả nhà cung cấp
    ];

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'creator_id',
        'user_id',
        'supplier_id',
    ];

    protected $guarded = [ 'id' ];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'           => 'string',
        'place_id'       => 'integer',
        'supplier_id'    => 'integer',
        'creator_id'     => 'integer',
        'user_id'        => 'integer',
        'amount'         => 'double',
        'debt'           => 'double',
        'paid'           => 'double',
        'attached_files' => 'string',
        'payment_method' => 'string',
        'note'           => 'string',
        'type'           => 'integer',
        'status'         => 'integer',
    ];

    // ======================= Overrided ================= //

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
     * {@inheritDoc}
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ======================= Mutators ================= //
    public function setCodeAttribute($value)
    {
        $this->attributes[ 'code' ] = is_null($value) ? $this->gencode($this->codePrefix[ $this->type ]) : $value;
    }

    // ======================= Relationships ================= //
    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Account::class, 'supplier_id');
    }

    public function supplies()
    {
        return $this->belongsToMany(Supply::class, 'inventory', 'inventory_order_id', 'supply_id')
            ->withPivot('qty_import', 'qty_export', 'qty_remain', 'total_price', 'price_pu')
            ->withTimestamps();
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('deleted_at', null);
    }
}
