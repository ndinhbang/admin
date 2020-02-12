<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use Carbon\Carbon;

class InventoryOrderFilter extends QueryFilter {
    public function type($search) {
        $search = is_null($search) ? 1 : $search;
        return $this->builder->where('inventory_orders.type', $search);
    }

    public function code($search) {
        if (is_null($search)) {
            return $this->builder;
        }

        return $this->builder->where('inventory_orders.code', 'like', "%{$search}%");
    }
    
    public function list($search) {
        if (is_null($search)) {
            return $this->builder;
        }

        switch ($search) {
            case 'all':
                # code...
                break;
            case 'debt':
                return $this->builder->where('debt', '>', 0)->whereNull('deleted_at');
                break;
            case 'complete':
                return $this->builder->whereNull('deleted_at')->where('status', 1);
                break;
            case 'draft':
                return $this->builder->whereNull('deleted_at')->where('status', 0);
                break;
            case 'trashed':
                return $this->builder->whereNotNull('deleted_at');
                break;
        }
    }
    public function keyword($search) {
        if (is_null($search)) {
            return $this->builder;
        }

        return $this->builder->where('inventory_orders.code', 'like', "%{$search}%");
    }
    public function start($search) {
        if (is_null($search)) {
            return $this->builder;
        }

        // date time range
        $startDate = Carbon::parse($search)->format('Y-m-d 00:00:00');
        $endDate = Carbon::parse(request()->get('end', Carbon::now()))->format('Y-m-d 23:59:59');

        return $this->builder->whereBetween('inventory_orders.created_at', [$startDate, $endDate]);
    }

}