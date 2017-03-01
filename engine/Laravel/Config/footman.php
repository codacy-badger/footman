<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Footman Default Request Type [String]
    |--------------------------------------------------------------------------
    |
    | Request Type Default Value, This value will be set on
    | Each request, But you can overwrite it in the closure.
    | Request Type must be one of these values [GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS]
    |
    */
    'request_type' => env('FOOTMAN_REQUEST_TYPE', 'GET'),

    /*
    |--------------------------------------------------------------------------
    | Footman Timeout [Float]
    |--------------------------------------------------------------------------
    |
    | Float describing the timeout of the request in seconds. 
    | Use 0 to wait indefinitely.
    |
    */
    'timeout' => env('FOOTMAN_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | Footman Connection Timeout [Int]
    |--------------------------------------------------------------------------
    |
    | Float describing the number of seconds to wait while trying to connect to a server.
    | Use 0 to wait indefinitely.
    |
    */
    'connect_timeout' => env('FOOTMAN_CONNECTION_TIMEOUT', 0),

    /*
    |--------------------------------------------------------------------------
    | Footman Delay [Int|Float]
    |--------------------------------------------------------------------------
    |
    | The number of milliseconds to delay before sending the request.
    |
    */
    'delay' => env('FOOTMAN_DELAY', 0),

    /*
    |--------------------------------------------------------------------------
    | Footman Allow Redirect [bool|array]
    |--------------------------------------------------------------------------
    |
    | Allow Redirect Default Value, This value will be set on
    | Each request, But you can overwrite it in the closure.
    |
    */
    'allow_redirects' => env('FOOTMAN_ALLOW_REDIRECT', true),

    /*
    |--------------------------------------------------------------------------
    | Footman Decode Content [Bool|String]
    |--------------------------------------------------------------------------
    |
    | This option can be used to control how content-encoded response bodies are handled.
    | By default, decode_content is set to true, meaning any gzipped or deflated 
    | Response will be decoded.
    |
    | When set to false, the body of a response is never decoded, 
    | Meaning the bytes pass through the handler unchanged.
    |
    | When set to a string (ex: gzip), the bytes of a response are decoded and 
    | The string value provided to the decode_content option is passed
    | As the Accept-Encoding header of the request.
    |
    */
    'decode_content' => env('FOOTMAN_DECODE_CONTENT', true),

    /*
    |--------------------------------------------------------------------------
    | Footman Force IP Resolve [String]
    |--------------------------------------------------------------------------
    |
    | Set to "v4" if you want the HTTP handlers to use only ipv4 protocol 
    | Or "v6" for ipv6 protocol.
    | Pass null to disable Force IP Resolve for a request.
    |
    */
    'force_ip_resolve' => env('FOOTMAN_IP_RESOLVE', null),

    /*
    |--------------------------------------------------------------------------
    | Footman Synchronous [Bool]
    |--------------------------------------------------------------------------
    |
    | Set to true to inform HTTP handlers that you intend on waiting on the response.
    | This can be useful for optimizations.
    | Pass null to disable Synchronous for a request.
    |
    */
    'synchronous' => env('FOOTMAN_SYNCHRONOUS', null),

    /*
    |--------------------------------------------------------------------------
    | Footman Certificate [String|Array]
    |--------------------------------------------------------------------------
    |
    | Set to a string to specify the path to a file containing a PEM formatted
    | Client side certificate. If a password is required, then set to an array 
    | Containing the path to the PEM file in the first array element followed 
    | By the password required for the certificate in the second array element.
    | Pass null to disable Certificate for a request.
    | ex : ['/path/server.pem', 'password']
    |
    */
    'cert' => null,

    /*
    |--------------------------------------------------------------------------
    | Footman Authenticate [String|Array]
    |--------------------------------------------------------------------------
    |
    | Pass an array of HTTP authentication parameters to use with the request.
    | The array must contain the username in index [0], the password in index [1],
    | And you can optionally provide a built-in authentication type in index [2].
    | Pass null to disable authentication for a request.
    | ex : ['username', 'password']
    |
    */
    'auth' => null,

    /*
    |--------------------------------------------------------------------------
    | Footman Header [Array]
    |--------------------------------------------------------------------------
    |
    | Associative array of headers to add to the request.
    | Each key is the name of a header, and each value is a 
    | String or array of strings representing the header field values.
    |
    */
    'headers' => [
        'User-Agent' => env(
            'FOOTMAN_USER_AGENT',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Footman Share Cookie [Bool|Array]
    |--------------------------------------------------------------------------
    |
    | Specifies whether or not cookies are used in a request or 
    | What cookie jar to use or what cookies to send.
    | Pass false to disable cookies for a request.
    |
    */
    'cookies' => [
        // Share Cookies in all requests
        'share' => true,
        // Cookie Type [file|jar]
        'type' => 'jar',
        // If you use [file] as a type you can
        // Set store_session_cookies to true to store session cookies in the cookie jar.
        'store_session_cookies' => true,
        // If you use [jar] as a type you can
        // Set strict_mode to true to throw exceptions when
        // Invalid cookies are added to the cookie jar.
        'strict_mode' => false
    ],
];
