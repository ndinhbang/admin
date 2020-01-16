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
    'allowedOriginsPatterns' => ['/localhost:\d/','/[a-z\.]*goido\.net/','/[a-z\.]*goido\.local/', '/192\.168\.[0-9\.:]*/'],
    'allowedHeaders' => ['Content-Type', 'X-Requested-With', 'Authorization', 'X-Place-Id', 'X-Socket-ID', 'X-Socket-Id'],
    'allowedMethods' => ['GET', 'POST', 'PUT','DELETE'],
    'exposedHeaders' => [],
    'maxAge' => 0,

];
