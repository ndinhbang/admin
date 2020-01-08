<?php

namespace App\Models;


use App\Model;
use App\Model\PromotionCustomers;
use App\Traits\Filterable;

/**
 * Class Promotion
 *
 * @package App\Models
 *
 * @property string $uuid             UUID;
 * @property string title             Tiêu đề chương trình khuyến mại
 * @property string description       Mô tả chương trình
 * @property string code              Mã chương trình khuyến mại
 * @property \DateTime start_date     Ngày bắt đầu áp dụng
 * @property \DateTime end_date       Ngày bắt đầu áp dụng;
 * @property boolean require_coupon   Yêu cầu nhập mã khuyến mại
 * @property string type              Loại áp dụng, theo hàng hoá hay hoá đơn
 * @method static $this create(array $array_merge)
 */
class Promotion extends Model
{

    use Filterable;

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $primaryKey = 'id';


    protected $fillable = ['uuid', 'place_id', 'title', 'description', 'code', 'quantity', 'require_coupon', 'type', 'start_date', 'end_date', 'status'];

    protected $hidden = [
        'id',
        'place_id',
    ];

//    protected $with = ;

    public function customers()
    {
        return $this->belongsToMany(Account::class, 'promotions_customers', 'promotion_id', 'customer_id')
            ->wherePivot('customer_id', "!=", 0)
            ->where('accounts.type', 'customer');
    }

    public function segments()
    {
        return $this->belongsToMany(Segment::class, 'promotions_customers', 'promotion_id', 'segment_id')
            ->wherePivot('segment_id', "!=", 0);
    }


    public function appliedAll()
    {
        return $this->hasMany(PromotionApplied::class)->whereNull(['product_id', 'category_id'])
            ->where('promotion_applieds.type', '=', 'product');
    }


    public function appliedProducts()
    {
        return $this->belongsToMany(Product::class, 'promotion_applieds', 'promotion_id', 'product_id')
            ->wherePivot('product_id', ">", 0)
            ->withPivot(['quantity', 'discount', 'unit'])
            ->where('promotion_applieds.type', '=', 'product');

    }

    public function appliedCategories()
    {
        return $this->belongsToMany(Category::class, 'promotion_applieds', 'promotion_id', 'category_id')
            ->wherePivot('category_id', ">", 0)
            ->withPivot(['quantity', 'discount', 'unit'])
            ->where('promotion_applieds.type', '=', 'product');

    }

    public function appliedOrders()
    {
        return $this->hasMany(PromotionApplied::class, 'promotion_id')
            ->where('promotion_applieds.type', '=', 'order');
    }


}
