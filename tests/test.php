<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Alshf\Footman;
use Alshf\Exceptions\FootmanResponseException;
use Alshf\Exceptions\FootmanCookiesException;
use Alshf\Exceptions\FootmanRequestException;

// This Example Login into Github
try {
    // Create Client and Setup common Options for all requests
    $client = new Footman([
        'cookies' => [
            // Share Cookie on All Request
            'share' => true,
            // If you use [file] as a type you can
            // Set store_session_cookies to true to store session cookies in the cookie jar.
            // When you use File Cookie type you must pass cookie_name in request method closure
            // ----------------------------------------
            // If you use [jar] as a type you can
            // Set strict_mode to true to throw exceptions when
            // Invalid cookies are added to the cookie jar.
            'type' => 'jar',
            'strict_mode' => true
        ],
        'header' => [
            'User-Agent' => 'Footman CURL'
        ],
        'allow_redirects' => true,
    ]);

    // Now Send Request on Github Login Page
    $response = $client->request(function ($request) {
        $request->request_type = 'GET';
        $request->request_url = 'https://github.com/login';
    });

    // Get authenticity_token in Github Login Page
    preg_match(
        '/input.*?authenticity_token".*?value="(.*?)"/',
        $response->getContents(),
        $matches
    );

    // Now Attemp to Login on Github
    $response = $client->request(function ($request) use ($matches) {
        $request->request_type = 'POST';
        $request->request_url = 'https://github.com/session';
        $request->form_params = [
            'login' => 'Your GitHub Username',
            'password' => 'Your Github Password',
            'commit' => 'Sign in',
            'utf8' => '✓',
            'authenticity_token' => $matches[1],
        ];
    });
} catch (FootmanRequestException $e) {
    // Catch All HTML & connection Errors, timeouts and etc...
    echo $e->getMessage();
} catch (FootmanCookiesException $e) {
    // Catch All Cookies Errors
    echo $e->getMessage();
}

try {
    dump($response->getHeaders());
    dump($response->hasHeader('content/type'));
    dump($response->getHeader('content/type'));
    dump($response->getbody());
    dump($response->getContents());
    dump($response->read(10));
    dump($response->getStatusCode());
    dump($response->getStatusPhrase());
    dump($response->seek(0));
} catch (FootmanResponseException $e) {
    // Catch All Response Errors
    echo $e->getMessage();
}
