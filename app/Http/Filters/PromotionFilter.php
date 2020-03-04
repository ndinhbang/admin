<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class PromotionFilter extends QueryFilter
{
    /**
     * @param  string  $search
     * @return Builder
     */
    public function keyword(string $search): Builder
    {
        if ( is_null($search) ) {
            return $this->builder;
        }
        return $this->builder
            ->where('promotions.name', 'like', "%{$search}%");
    }
}
