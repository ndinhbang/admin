<?php

declare(strict_types=1);

namespace App\Ext\Eventually\Relations;

class MorphToMany extends \Illuminate\Database\Eloquent\Relations\MorphToMany
{
    use Concerns\InteractsWithPivotTable;
}
