<?php


namespace App;


/**
 * @method $this orderBy(string $column, string $order)
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    public static function findUuid($uuid)
    {
        return $uuid ? static::first('uuid', $uuid) : null;
    }
}
