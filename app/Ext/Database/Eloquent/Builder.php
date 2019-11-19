<?php

namespace App\Ext\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder as BaseEloquentBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class Builder extends BaseEloquentBuilder
{
    /**
     * Parse a list of relations into individuals.
     *
     * @param  array $relations
     * @param        $column
     * @param        $alias
     * @return array
     * @throws \Exception
     */
    protected function parseWithSumRelations(array $relations, &$column, &$alias)
    {
        $results = [];

        foreach ($relations as $name => $constraints) {
            // If the "name" value is a numeric key, we can assume that no
            // constraints have been specified. We'll just put an empty
            // Closure there, so that we can treat them all the same.
            if (is_numeric($name)) {
                $name = $constraints;

                list($constraints) = [function () {
                    //
                }];
            }

            // First we will determine if the name has been aliased using an "as" clause on the name
            // and if it has we will extract the actual relationship name and the desired name of
            // the resulting column. This allows multiple counts on the same relationship name.
            $segments = explode(' ', $name);

            if (count($segments) === 3 && Str::lower($segments[1]) === 'as') {
                list($name, $alias) = [$segments[0], $segments[2]];
            }

            // Next we will determine if the name has column for sum using an ":" on the name
            if (count($segments = explode(':', $name)) < 2) {
                throw new \Exception('No column found. You need to provide a column for withSum() to work.');
            }

            list($name, $column) = [$segments[0], $segments[1]];

            // We need to separate out any nested includes, which allows the developers
            // to load deep relationships using "dots" without stating each level of
            // the relationship with its own key in the array of eager-load names.
            $results = $this->addNestedWiths($name, $results);

            $results[$name] = $constraints;
        }

        return $results;
    }

    /**
     * Add subselect queries to sum a specific column of the relation.
     * Inspire of withCount
     *
     * @param  mixed $relations
     * @return $this
     * @throws \Exception
     */
    public function withSum($relations)
    {
        if (empty($relations)) {
            return $this;
        }

        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from . '.*']);
        }

        $relations = is_array($relations) ? $relations : func_get_args();

        $sumColumn = $sumAlias = null;

        foreach ($this->parseWithSumRelations($relations, $sumColumn, $sumAlias) as $name => $constraints) {

            $relation = $this->getRelationWithoutConstraints($name);

            // Here we will get the relationship count query and prepare to add it to the main query
            // as a sub-select. First, we'll get the "has" query and use that to get the relation
            // count query. We will normalize the relation name then append _sum as the name.
            $query = $relation->getRelationExistenceQuery(
                $relation->getRelated()->newQuery(), $this, new Expression('sum(' . $sumColumn . ')')
            )->setBindings([], 'select');

            $query->callScope($constraints);

            $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();

            if (count($query->columns) > 1) {
                $query->columns = [$query->columns[0]];
            }

            // Finally we will add the proper result column alias to the query and run the subselect
            // statement against the query builder. Then we will return the builder instance back
            // to the developer for further constraint chaining that needs to take place on it.
            $column = $sumAlias ?? Str::snake($name . '_' . $sumColumn . '_sum');

            $this->selectSub($query, $column);
        }

        unset($sumColumn, $sumAlias);

        return $this;
    }
}
