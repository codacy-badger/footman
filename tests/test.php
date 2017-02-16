<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Alshf\RequestProvider;
use Alshf\Exceptions\RequestProviderException;

try {
    $client = new RequestProvider;

    $response = $client->request(function ($request) {
        $request->header = [
            'User-Agent' => 'Footman User-Agent'
        ];

        $request->allow_redirects = [
            'max'              => 10,
            'strict'          => false,
            'referer'         => false,
            'protocols'       => ['http', 'https'],
            'track_redirects' => false
        ];

        $request->request_type = 'POST';
        $request->request_url = 'https://google.com/';

        // Form Data For POST Request
        $request->form_params = [
            'foo' => 'bar',
            'baz' => ['hi', 'there!']
        ]
    });

    dump($response->getHeaders());
    dump($response->getHeader('content/type'));
    dump($response->hasHeader('content/type'));
    dump($response->getbody());
    dump($response->getContents());
    dump($response->read(10));
    dump($response->getStatus());
    dump($response->getStatusPhrase());
} catch (RequestProviderException $e) {
    dump($e->getMessage());
}
