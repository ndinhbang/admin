<?php

namespace App\Scopes;

use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PlaceM2MScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (!is_null($place = (request()->place ?? null))) {
            $builder->whereHas('places', function ($query) use ($place) {
                $query->where('places.id', $place->id);
            });
        }
    }
}