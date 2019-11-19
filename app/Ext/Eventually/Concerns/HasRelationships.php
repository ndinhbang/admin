<?php

declare(strict_types=1);

namespace App\Ext\Eventually\Concerns;

use App\Ext\Eventually\Relations\BelongsToMany;
use App\Ext\Eventually\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasRelationships
{
    /**
     * Instantiate a new BelongsToMany relationship.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model   $parent
     * @param string                                $table
     * @param string                                $foreignPivotKey
     * @param string                                $relatedPivotKey
     * @param string                                $parentKey
     * @param string                                $relatedKey
     * @param string                                $relationName
     *
     * @return BelongsToMany
     */
    protected function newBelongsToMany(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null
    ) {
        return new BelongsToMany(
            $query,
            $parent,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relationName
        );
    }

    /**
     * Instantiate a new MorphToMany relationship.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model   $parent
     * @param string                                $name
     * @param string                                $table
     * @param string                                $foreignPivotKey
     * @param string                                $relatedPivotKey
     * @param string                                $parentKey
     * @param string                                $relatedKey
     * @param string                                $relationName
     * @param bool                                  $inverse
     *
     * @return MorphToMany
     */
    protected function newMorphToMany(
        Builder $query,
        Model $parent,
        $name,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
        $inverse = false
    ) {
        return new MorphToMany(
            $query,
            $parent,
            $name,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relationName,
            $inverse
        );
    }
}
