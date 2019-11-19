<?php

declare(strict_types=1);

namespace App\Ext\Eventually\Relations;

class BelongsToMany extends \Illuminate\Database\Eloquent\Relations\BelongsToMany
{
    use Concerns\InteractsWithPivotTable;
}
