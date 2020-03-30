<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use App\Http\Requests\CategoryRequest;

class CategoryFilter extends QueryFilter
{
    public function __construct(CategoryRequest $request)
    {
        parent::__construct($request);
    }

    public function name($search)
    {
        if (is_null($search)) {
            return $this->builder;
        }

        return $this->builder->where('categories.name', 'like', "%{$search}%");
    }

    public function type($type)
    {
        if (is_null($type)) {
            return $this->builder;
        }

        return $this->builder->where('categories.type', $type);
    }

    public function state($status)
    {
        if (is_null($status)) {
            return $this->builder;
        }

        return $this->builder->where('categories.state', (int)$status);
    }
}