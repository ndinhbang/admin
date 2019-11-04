<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\AppendPlace;

class Role extends SpatieRole
{
    use AppendPlace;
}
