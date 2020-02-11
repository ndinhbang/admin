<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use Carbon\Carbon;

class InventoryTakeFilter extends QueryFilter {

    public function code($search) {
        if (is_null($search)) {
            return $this->builder;
        }

        return $this->builder->where('inventory_takes.code', 'like', "%{$search}%");
    }

}