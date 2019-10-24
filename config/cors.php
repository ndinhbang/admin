<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
   
    'supportsCredentials' => false,
    'allowedOrigins' => ['http://localhost:8000'],
    'allowedOriginsPatterns' => ['/localhost:\d/','/[a-z\.]*goido\.local/'],
    'allowedHeaders' => ['Content-Type', 'X-Requested-With', 'Authorization'],
    'allowedMethods' => ['GET', 'POST', 'PUT','DELETE'],
    'exposedHeaders' => [],
    'maxAge' => 0,

];
