<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;

class AccountFilter extends QueryFilter
{
    public function keyword($search)
    {
        if (is_null($search)) {
            return $this->builder;
        }

        return $this->builder->where(function($query) use ($search) {
            $query->where('accounts.code', 'like', "%{$search}%")
                ->orWhere('accounts.unsigned_name', 'like', "%{$search}%")
                ->orWhere('accounts.phone', 'like', "%{$search}%");
        });
    }

    public function type($type)
    {
        if (is_null($type)) {
            return $this->builder;
        }

        return $this->builder->where('accounts.type', $type);
    }

    public function state($status)
    {
        if (is_null($status)) {
            return $this->builder;
        }

        return $this->builder->where('categories.state', (int)$status);
    }
}
