<?php

namespace App\Models;

use App\Traits\AppendPlace;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use AppendPlace;

    protected $hidden = [
        'id',
        'place_id',
        'guard_name',
        'level',
        'created_at',
        'pivot',
        'permissions',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Scope a query to include guard name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null                           $guardName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfGuard(Builder $query, $guardName = null)
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        return $query->where('guard_name', $guardName);
    }

    /**
     * Query roles by uuid(s)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $uuids
     * @param string|null                           $guardName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByUuids(Builder $query, array $uuids, $guardName = null): Builder
    {
        return $query->whereIn('uuid', $uuids)->ofGuard($guardName);
    }
}
