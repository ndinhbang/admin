<?php

namespace App\Scopes;

use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PlaceScope implements Scope
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

        if (!is_null($uuid = request()->header('X-Place-Id'))) {
            $placeId = request()->place->id ?? 0;
            $builder->where('place_id', $placeId);
        }
    }
}