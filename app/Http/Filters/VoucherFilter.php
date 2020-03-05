<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;
use Carbon\Carbon;

class VoucherFilter extends QueryFilter
{
    protected function beforeApplied()
    {
        $this->builder->whereBetween('vouchers.created_at', [
            Carbon::parse($this->request->start ?? Carbon::now())->format('Y-m-d 00:00:00'),
            Carbon::parse($this->request->end ?? Carbon::now())->format('Y-m-d 23:59:59')
        ]);
    }

    public function keyword($search)
    {
        if ( is_null($search) ) {
            return $this->builder;
        }
        return $this->builder
            ->where('vouchers.code', 'like', "%{$search}%")
            ->orWhere('vouchers.title', 'like', "%{$search}%");
    }

    public function type($type)
    {
        if ( is_null($type) || $type == 9) {
            return $this->builder;
        }
        if ( in_array($type, [ 0, 1 ]) ) {
            return $this->builder->where('vouchers.type', $type);
        }

        return $this->builder->onlyTrashed();
    }
}
