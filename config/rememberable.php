<?php

return [

    'enabled' => env('REMEMBERABLE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Query Logging
    |--------------------------------------------------------------------------
    |
    | Log query and its equivalent cache key to Debugbar message tab.
    | This function is disabled by default.
    |
    */

    'query_log' => env('REMEMBERABLE_QUERY_LOG', false),
];
