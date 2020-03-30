<?php

declare(strict_types=1);

namespace App\Ext\Eventually\Relations\Concerns;

trait InteractsWithPivotTable
{
    use \Altek\Eventually\Relations\Concerns\InteractsWithPivotTable;

    /**
     * Override sync method from BaseInteractsWithPivotTable trait
     *
     * @param mixed $ids
     * @param bool  $detaching
     *
     * @return array|bool
     */
    public function sync($ids, $detaching = true)
    {
        $properties = $this->compilePivotProperties($ids);

        if ($this->parent->firePivotEvent('syncing', true, $this->getRelationName(), $properties) === false) {
            return false;
        }

        $changes = parent::sync($ids, $detaching);

        // only fire a "synced" event when any of the pivot relations has been edited
        if(!empty(array_filter($changes))) {
            $this->parent->firePivotEvent('synced', false, $this->getRelationName(), $properties);
        }

        return $changes;
    }
}
