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
            // many to many
            if ($model->relationLoaded('places')) {
                $builder->whereHas('places', function ($query) use ($placeId) {
                    $query->where('places.id', $placeId);
                });
                // one to many
            } elseif ($model->relationLoaded('place')) {
                $builder->whereHas('place', function ($query) use ($placeId) {
                    $query->where('places.id', $placeId);
                });
                // query by place_id collumn
            } else {
                $builder->where($model->getTable() . '.place_id', $placeId);
            }
        }
    }
}