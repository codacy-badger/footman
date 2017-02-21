<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Footman Allow Redirect [bool|array]
    |--------------------------------------------------------------------------
    |
    | Allow Redirect Default Value, This value will be set on
    | each request, But you can overwrite it in the closure
    |
    */
   'allow_redirects' => env('FOOTMAN_REDIRECT', true),

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
    | Footman Default Request Type [String]
    |--------------------------------------------------------------------------
    |
    | Request Type Default Value, This value will be set on
    | each request, But you can overwrite it in the closure.
    | Request Type must be one of these values [GET, POST, PUT, PATCH, DELETE]
    |
    */
   'request_type' => env('FOOTMAN_REQUEST_TYPE', 'GET'),
];
