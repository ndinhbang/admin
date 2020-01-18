<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use Carbon\Carbon;

class OrderFilter extends QueryFilter
{
    public function code($search)
    {
        if ( is_null($search) ) {
            return $this->builder;
        }
        return $this->builder->where('orders.code', 'like', "%{$search}%");
    }

    public function keyword($search)
    {
        if ( is_null($search) ) {
            return $this->builder;
        }
        return $this->builder->where('orders.code', 'like', "%{$search}%");
    }

    public function start($search)
    {
        if ( is_null($search) ) {
            return $this->builder;
        }
        // date time range
        $startDate = Carbon::parse($search)
            ->format('Y-m-d 00:00:00');
        $endDate   = Carbon::parse(request()->get('end', Carbon::now()))
            ->format('Y-m-d 23:59:59');
        return $this->builder
            ->whereBetween('orders.created_at', [
                $startDate,
                $endDate,
            ]);
    }

    public function progresing($isProgressing)
    {
        if ( !$isProgressing ) {
            return $this->builder;
        }
        return $this->builder->progressing();
    }

    public function kind($kind)
    {
        if ( is_null($kind) ) {
            return $this->builder;
        }
        return $this->builder->where('kind', (int) $kind);
    }

    public function canceled($kind)
    {
        if ( !$kind ) {
            return $this->builder;
        }
        return $this->builder->where('is_canceled', 1);
    }

    public function served($kind)
    {
        if ( !$kind ) {
            return $this->builder;
        }
        return $this->builder->where('is_served', 1);
    }

    public function completed($kind)
    {
        if ( !$kind ) {
            return $this->builder;
        }
        return $this->builder->where('is_completed', 1);
    }

    public function returned($kind)
    {
        if ( !$kind ) {
            return $this->builder;
        }
        return $this->builder->where('is_returned', 1);
    }

    public function paid($kind)
    {
        if ( !$kind ) {
            return $this->builder;
        }
        return $this->builder->where('is_paid', 1);
    }

    public function active($isActive)
    {
        if ( !$isActive ) {
            return $this->builder;
        }
        return $this->builder
            ->where(function ($query) {
                $query->where('is_paid', 0)
                    ->where('is_returned', 0)
                    ->where('is_completed', 0)
                    ->where('is_canceled', 0);
            });
    }

    public function inactive($isNotActive)
    {
        if ( !$isNotActive ) {
            return $this->builder;
        }
        return $this->builder
            ->where(function ($query) {
                $query->where('is_paid', 1)
                    ->orWhere('is_returned', 1)
                    ->orWhere('is_completed', 1)
                    ->orWhere('is_canceled', 1);
            });
    }

    public function day($start)
    {
        if ( !$start || $start == 'today' ) {
            $today = Carbon::today();
            return $this->builder->where('day', $today->day)
                ->where('month', $today->month)
                ->where('year', $today->year);
        }
        $time = Carbon::parse($start);
        return $this->builder->where('day', $time->day)
            ->where('month', $time->month)
            ->where('year', $time->year);
    }

}
