<?php

namespace App\Models;

use App\Model;
use App\Scopes\PlaceScope;
use App\Traits\Filterable;

class Criterion extends Model
{
    protected $table = 'segments_criteria';

    use Filterable;

    protected $hidden = [
        'place_id', 'segment_id'
    ];

    protected $fillable = [
        'uuid', 'segment_id', 'place_id', 'property', 'operator', 'value'
    ];


    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new PlaceScope);
    }


    protected $properties = [
        'total_amount' => 'Tổng số lượng',
        'total_paid'   => 'Tổng số tiền',
        'total_debt'   => 'Tổng nợ',
        'birth_month'  => 'Tháng sinh',
        'last_order'   => 'Lần cuối mua hàng',
        'gender'       => 'Giới tính',
    ];


    public function getPropertyNameAttribute()
    {
        return $this->properties[$this->property];
    }
}
