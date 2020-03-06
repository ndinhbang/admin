<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GenerateCode;

/**
 * @method static create( array $voucherData )
 */
class Voucher extends Model
{
    use SoftDeletes, GenerateCode, Filterable;

    protected $primaryKey = 'id';
    protected $table = 'vouchers';

    protected $codePrefix = [
        1 => 'PT',
        // Phiếu thu
        0 => 'PC',
        // Phiếu chi
    ];

    protected $name = [
        1 => 'thu',
        0 => 'chi',
    ];

    protected $guarded = [ 'id' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'place_id',
        'category_id',
        'payer_payee_id',
        'creator_id',
        'approver_id',
        'order_id',
        'inventory_order_id',
    ];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'           => 'string',
        'place_id'       => 'integer',
        'category_id'    => 'integer',
        'payer_payee_id' => 'integer',
        'payment_method' => 'string',
        'title'          => 'string',
        'amount'         => 'double',
        'type'           => 'integer',
        'note'           => 'string',
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // ======================= Mutators ================= //
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = is_null($value)
            ? $this->gencode($this->codePrefix[ $this->type ])
            : $value;
    }

    /**
     * The roles that belong to the user.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }

    public function approver()
    {
        return $this->belongsTo('App\User', 'approver_id');
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
}
