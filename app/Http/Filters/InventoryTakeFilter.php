<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use Carbon\Carbon;

class InventoryTakeFilter extends QueryFilter {

    public function code($code) {
        if (is_null($code)) {
            return $this->builder;
        }

        return $this->builder->where('inventory_takes.code', 'like', "%{$code}%");
    }

    public function employee_uuids($employee_uuids) {
        if (is_null($employee_uuids)) {
            return $this->builder;
        }

        return $this->builder->whereHas('creator', function ($query) use ($employee_uuids) {
            $query->whereIn('users.uuid', $employee_uuids);
        });
    }

}