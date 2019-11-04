<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\AppendPlace;

class Permission extends SpatiePermission
{
    use AppendPlace;

//    protected $guard_name = 'api';
}
