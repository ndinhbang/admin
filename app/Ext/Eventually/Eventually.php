<?php

declare(strict_types=1);

namespace App\Ext\Eventually;

use Altek\Eventually\Concerns\HasEvents;
use App\Ext\Eventually\Concerns\HasRelationships;

trait Eventually
{
    use HasEvents;
    use HasRelationships;
}
