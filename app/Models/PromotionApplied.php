<?php

namespace App\Models;


use App\Model;

/**
 * Class PromotionApplied
 *
 * @package App\Models
 * @property int $id
 * @property int $promotion_id
 * @property string $type Value in enum 'product', 'order'
 * @property int $category_id
 * @property int $product_id
 * @property int quantity
 * @property int discount
 * @property string unit
 */
class PromotionApplied extends Model
{

    public $timestamps = false;

    protected $table = 'promotion_applieds';

    protected $fillable = ['promotion_id', 'type', 'quantity', 'discount', 'unit', 'category_id', 'product_id'];
}
