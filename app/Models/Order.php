<?php

namespace App\Models;

use App\Scopes\PlaceScope;
use App\Traits\Filterable;
use App\Traits\GenerateCode;
use App\Traits\HasVoucher;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property float|int     amount
 * @property int           total_dish
 * @property mixed         id
 * @property int           is_canceled
 * @property mixed|string  reason
 * @property int|mixed     total_eater
 * @property mixed|string  note
 * @property int|mixed     customer_id
 * @property int|mixed     paid
 * @property int|mixed     debt
 * @property bool          is_paid
 * @property int|mixed     received_amount
 * @property mixed         is_returned
 * @property mixed         is_completed
 * @property mixed|string  card_name
 * @property mixed         place_id
 * @property  integer|null table_id
 * @property int           discount_items_amount
 * @property int|mixed     discount_amount
 * @method static select( string $string )
 * @method static create( array $array_merge )
 */
class Order extends Model
{
    use Filterable, SoftDeletes, HasVoucher, GenerateCode;

    protected $table = 'orders';
    protected $codePrefix = 'HD';

    // ======================= Hidden Attributes ================= //
    protected $hidden = [
        'id',
        'place_id',
        'creator_id',
        'customer_id',
        'table_id',
        'promotion_id',
    ];

    // ======================= Attribute Casting ================= //
    protected $casts = [
        'uuid'                  => 'string',
        'code'                  => 'string',
        'place_id'              => 'integer',
        'creator_id'            => 'integer',
        'customer_id'           => 'integer',
        'table_id'              => 'integer',
        'state'                 => 'integer',
        'kind'                  => 'integer',
        'total_dish'            => 'integer',
        'total_eater'           => 'integer',
        'note'                  => 'string',
        'reason'                => 'string',
        'received_amount'       => 'integer',
        'discount_amount'       => 'integer',
        'discount_items_amount' => 'integer',
        'debt'                  => 'integer',
        'paid'                  => 'integer',
        'amount'                => 'integer',
        'day'                   => 'integer',
        'month'                 => 'integer',
        'year'                  => 'integer',
        'is_returned'           => 'boolean',
        'is_canceled'           => 'boolean',
        'is_served'             => 'boolean',
        'is_paid'               => 'boolean',
        'is_completed'          => 'boolean',
        'card_name'             => 'string',
        'promotion_id'          => 'integer',
        'promotion_uuid'        => 'string',
        'promotion_automated'   => 'boolean',
    ];

    /**
     * Default values for attributes
     * Note: Keep it in sync with default values that you set for filed in database
     *
     * @var  array
     */
    protected $attributes = [
        'state'                 => 0,
        'amount'                => 0,
        'received_amount'       => 0,
        'discount_amount'       => 0,
        'discount_items_amount' => 0,
        'debt'                  => 0,
        'paid'                  => 0,
        'total_dish'            => 0,
        'total_eater'           => 0,
        'is_returned'           => false,
        'is_canceled'           => false,
        'is_served'             => false,
        'is_paid'               => false,
        'is_completed'          => false,
        'type'                  => 1,
        // 1: đã bán, 0: trả lại
    ];

    protected $guarded = [ 'id' ];

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
        $this->attributes[ 'code' ] = is_null($value) ? $this->gencode($this->codePrefix) : $value;
    }

    // ======================= Local Scopes ================= //
    public function scopeProgressing($query)
    {
        return $query->where('is_returned', 0)
            ->where('is_canceled', 0)
            ->where('is_served', 0)
            ->where('is_completed', 0);
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

    public function customer()
    {
        return $this->belongsTo(Account::class, 'customer_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
            ->withTimestamps();
    }

    public function supplies()
    {
        return $this->belongsToMany(Supply::class, 'inventory', 'order_id', 'supply_id')
            ->withPivot('qty_import', 'qty_export', 'qty_remain', 'total_price', 'price_pu')
            ->withTimestamps();
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    /**
     * Sync thông tin khuyến mãi
     *
     * @param  \Illuminate\Support\Collection  $promotions
     * @param  array                           $data
     */
    public function syncPromotions(Collection $promotions, array $data)
    {
        if ( empty($data[ 'promotions' ]) ) {
            return;
        }
        $promotionArr = collect($data[ 'promotions' ])->keyBy('uuid')->all();
        $this->promotions()->sync(
            $promotions->mapWithKeys(
                function ($row) use ($promotionArr) {
                    return [
                        $row[ 'id' ] => [
                            'discount_amount' => $promotionArr[ $row[ 'uuid' ] ][ 'discount_amount' ],
                        ],
                    ];
                }
            )->all()
        );
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_detail', 'order_id', 'promotion_id')
            ->withPivot([ 'discount_amount' ])
            ->withTimestamps();
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
