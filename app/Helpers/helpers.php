<?php

if (! function_exists('nanoId')) {
    function nanoId() {
        $client = new \Hidehalo\Nanoid\Client();
        return $client->generateId($size = 21, $mode = \Hidehalo\Nanoid\Client::MODE_DYNAMIC);
    }
}

if (! function_exists('currentPlace')) {
    function currentPlace() {
        return app()->offsetExists('currentPlace') ? resolve('currentPlace') : null;
    }
}