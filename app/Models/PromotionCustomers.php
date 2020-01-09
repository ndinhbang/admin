<?php

namespace App\Model;

use App\Model;

/**
 * Class PromotionCustomers
 *
 * @package App\Model
 *
 * @property int id
 * @property int $promotion_id
 * @property int $segment_id
 * @property int customer_id
 */
class PromotionCustomers extends Model
{
    protected $table = 'promotions_customers';
}
