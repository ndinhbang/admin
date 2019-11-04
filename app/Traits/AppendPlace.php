<?php
namespace App\Traits;

trait AppendPlace
{
    public static function bootAppendPlace()
    {
        static::addGlobalScope(new \App\Scopes\PermissionScope());
    }
}