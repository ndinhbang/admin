<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;

class OrderFilter extends QueryFilter
{
    public function code($search)
    {
        if (is_null($search)) {
            return $this->builder;
        }

        return $this->builder->where('orders.code', 'like', "%{$search}%");
    }

    public function progresing($isProgressing)
    {
        if (!$isProgressing) {
            return $this->builder;
        }

        return $this->builder->progressing();
    }

    public function kind($kind)
    {
        if (is_null($kind)) {
            return $this->builder;
        }

        return $this->builder->where('kind', (int) $kind);
    }

    public function canceled($kind)
    {
        if (!$kind) {
            return $this->builder;
        }

        return $this->builder->where('is_canceled', 1);
    }

    public function served($kind)
    {
        if (!$kind) {
            return $this->builder;
        }

        return $this->builder->where('is_served', 1);
    }

    public function completed($kind)
    {
        if (!$kind) {
            return $this->builder;
        }

        return $this->builder->where('is_completed', 1);
    }

    public function returned($kind)
    {
        if (!$kind) {
            return $this->builder;
        }

        return $this->builder->where('is_returned', 1);
    }

    public function paid($kind)
    {
        if (!$kind) {
            return $this->builder;
        }

        return $this->builder->where('is_paid', 1);
    }

}