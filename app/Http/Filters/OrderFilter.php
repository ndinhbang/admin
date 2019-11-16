<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use App\Http\Requests\PosRequest;

class OrderFilter extends QueryFilter
{
    public function __construct(PosRequest $request)
    {
        parent::__construct($request);
    }

    public function code($search)
    {
        if (is_null($search)) {
            return $this->builder;
        }

        return $this->builder->where('orders.code', 'like', "%{$search}%");
    }

}