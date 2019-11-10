<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;

class ProductFilter extends QueryFilter
{
    public function name($search)
    {
        if (is_null($search)) {
            return $this->builder;
        }

        return $this->builder->where('products.name', 'like', "%{$search}%");
    }

    public function is_hot($is_hot)
    {
        if (is_null($is_hot)) {
            return $this->builder;
        }

        return $this->builder->where('products.is_hot', (int)$is_hot);
    }

    public function state($status)
    {
        if (is_null($status)) {
            return $this->builder;
        }

        return $this->builder->where('products.state', (int)$status);
    }

    public function category_id($category_id)
    {
        if (is_null($category_id)) {
            return $this->builder;
        }

        return $this->builder->where('products.category_id', (int)$category_id);
    }
}