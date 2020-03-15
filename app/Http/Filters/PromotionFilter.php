<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PromotionFilter extends QueryFilter
{
    /**
     * @param  string  $search
     * @return Builder
     */
    public function keyword(?string $search): Builder
    {
        if ( is_null($search) ) {
            return $this->builder;
        }
        return $this->builder
            ->where('promotions.name', 'like', "%{$search}%");
    }

    /**
     * @param $date
     * @return Builder
     */
    public function t($date): Builder
    {
        if ( is_null($date) ) {
            return $this->builder;
        }
        $currentDate = Carbon::parse($date)->format('Y-m-d H:i:s');
        return $this->builder
            ->where('promotions.from', '<=', $currentDate)
            ->where(
                function ($query) use ($currentDate) {
                    $query->whereNull('promotions.to')
                        ->orWhere('promotions.to', '>=', $currentDate);
                }
            );
    }
}
