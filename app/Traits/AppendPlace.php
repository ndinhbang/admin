<?php
namespace App\Traits;

trait AppendPlace
{
    public static function bootAppendPlace()
    {
        if(!request()->is('api/admin/*'))
            static::addGlobalScope(new \App\Scopes\PermissionScope());
    }
}