<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PlaceScope implements Scope {
	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model    $model
	 * @return void
	 * @throws \Exception
	 */
	public function apply(Builder $builder, Model $model) {
		if ($model->getTable() == 'categories') {
			$builder->where($model->getTable() . '.place_id', currentPlace()->id)->orWhere($model->getTable() . '.place_id', 0);
		} else {
			$builder->where($model->getTable() . '.place_id', currentPlace()->id);
		}
	}
}