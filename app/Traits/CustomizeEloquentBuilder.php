<?php

namespace App\Traits;

use App\Ext\Database\Eloquent\Builder;
use App\Ext\Database\Eloquent\Collection;

trait CustomizeEloquentBuilder {
	/**
	 * The relationship sums that should be eager loaded on every query.
	 *
	 * @var array
	 */
	protected $withSum = [];

	/**
	 * Create a new Eloquent query builder for the model.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function newEloquentBuilder($query) {
		return new Builder($query);
	}

	/**
	 * Get a new query builder that doesn't have any global scopes.
	 *
	 * @return \Illuminate\Database\Eloquent\Builder|static
	 */
	public function newQueryWithoutScopes() {
		return $this->newModelQuery()
			->with($this->with)
			->withCount($this->withCount)
			->withSum($this->withSum);
	}

	/**
	 * Create a new Eloquent Collection instance.
	 *
	 * @param  array  $models
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function newCollection(array $models = []) {
		return new Collection($models);
	}
}
