<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\AppendPlace;

class Permission extends SpatiePermission
{
    use AppendPlace;

    protected $hidden = ['id', 'place_id', 'guard_name', 'pivot', 'created_at'];

}
