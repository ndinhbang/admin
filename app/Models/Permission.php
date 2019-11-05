<?php

namespace App\Models;

use App\Traits\AppendPlace;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use AppendPlace;

    protected $hidden = [
        'id',
        'place_id',
        'guard_name',
        'pivot',
        'created_at',
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

}
