<?php

namespace App\Http\Filters;

use App\Concerns\QueryFilter;

class ProductFilter extends QueryFilter {
	public function keyword($search) {
		if (is_null($search)) {
			return $this->builder;
		}

		return $this->builder
			->where('code', 'like', "%{$search}%")
			->orWhere('name', 'like', "%{$search}%");

	}

	public function is_hot($is_hot) {
		if (is_null($is_hot)) {
			return $this->builder;
		}

		return $this->builder->where('products.is_hot', (int) $is_hot);
	}

	public function state($status) {
		if (is_null($status)) {
			return $this->builder;
		}

		return $this->builder->where('products.state', (int) $status);
	}

	public function opened($opened) {
		if (is_null($opened)) {
			return $this->builder;
		}

		return $this->builder->where('products.opened', (int) $opened);
	}

	public function category_uuids($category_uuids) {
		if (is_null($category_uuids)) {
			return $this->builder;
		}

		return $this->builder->whereHas('category', function ($query) use ($category_uuids) {
			$query->whereIn('categories.uuid', $category_uuids);
		});
	}
}