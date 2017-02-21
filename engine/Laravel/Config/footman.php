<?php
return [
   /*
    |--------------------------------------------------------------------------
    | Footman Default Request Type [String]
    |--------------------------------------------------------------------------
    |
    | Request Type Default Value, This value will be set on
    | each request, But you can overwrite it in the closure.
    | Request Type must be one of these values [GET, POST, PUT, PATCH, DELETE]
    |
    */
   'request_type' => env('FOOTMAN_REQUEST_TYPE', 'GET'),

    /*
    |--------------------------------------------------------------------------
    | Footman Timeout [Int]
    |--------------------------------------------------------------------------
    |
    | Timeout Default Value, This value will be set on
    | each request, But you can overwrite it in the closure.
    | The Request Will be Timeout in Seconds
    |
    */
   'timeout' => env('FOOTMAN_TIMEOUT', 5),

   /*
    |--------------------------------------------------------------------------
    | Footman Allow Redirect [bool|array]
    |--------------------------------------------------------------------------
    |
    | Allow Redirect Default Value, This value will be set on
    | each request, But you can overwrite it in the closure
    |
    */
   'allow_redirects' => [
        'max'             => 5,
        'strict'          => true,
        'referer'         => true,
        'protocols'       => ['http', 'https'],
        'track_redirects' => true
    ],
];
