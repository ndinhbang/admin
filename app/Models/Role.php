<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\AppendPlace;

class Role extends SpatieRole
{
    use AppendPlace;

    protected $hidden = ['id', 'place_id', 'guard_name', 'pivot', 'created_at'];

    /**
     * Query roles by uuid(s)
     *
     * @param array $uuids
     * @param string|null  $guardName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function findByUuids(array $uuids, $guardName = null): Builder
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        return static::whereIn('uuid', $uuids)->where('guard_name', $guardName);
    }
}
