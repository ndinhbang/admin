<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use App\Http\Requests\AreaRequest;

class AreaFilter extends QueryFilter
{
    public function __construct(AreaRequest $request)
    {
        parent::__construct($request);
    }

    public function with($relation)
    {
        if (is_null($relation)) {
            return $this->builder;
        }

        return $this->builder->with($relation);
    }
}