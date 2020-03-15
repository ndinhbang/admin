<?php

namespace App\Ext\Database\Eloquent;

use Illuminate\Database\Eloquent\Collection as BaseEloquentCollection;
use Illuminate\Support\Arr;

class Collection extends BaseEloquentCollection
{
    /**
     * Load a set of relationship sums onto the collection.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadSum($relations)
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $models = $this->first()->newModelQuery()
            ->whereKey($this->modelKeys())
            ->select($this->first()->getKeyName())
            ->withSum(...func_get_args())
            ->get();

        $attributes = Arr::except(
            array_keys($models->first()->getAttributes()),
            $models->first()->getKeyName()
        );

        $models->each(function ($model) use ($attributes) {
            $this->find($model->getKey())->forceFill(
                Arr::only($model->getAttributes(), $attributes)
            )->syncOriginalAttributes($attributes);
        });

        return $this;
    }
}
