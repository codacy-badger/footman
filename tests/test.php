<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Alshf\Footman;
// use Alshf\Exceptions\FootmanException;
// use Alshf\Exceptions\FootmanResponseException;

// try {
//     $client = new Footman;

//     $response = $client->request(function ($request) {
//         $request->header = [
//             'User-Agent' => 'Footman User-Agent'
//         ];

//         $request->allow_redirects = [
//             'max'              => 10,
//             'strict'          => false,
//             'referer'         => false,
//             'protocols'       => ['http', 'https'],
//             'track_redirects' => false
//         ];

//         $request->request_type = 'POST';
//         $request->request_url = 'https://google.com/';

//         // Form Data For POST Request
//         $request->form_params = [
//             'foo' => 'bar',
//             'baz' => ['hi', 'there!']
//         ];
//     });

//     dump($response->getHeaders());
//     dump($response->getHeader('content/type'));
//     dump($response->hasHeader('content/type'));
//     dump($response->getbody());
//     dump($response->getContents());
//     dump($response->read(10));
//     dump($response->getStatusCode());
//     dump($response->getStatusPhrase());
// } catch (FootmanException $e) {
//     dump($e->getMessage());
// } catch (FootmanResponseException $e) {
//     dump($e->getMessage());
// }
// 

$client = new Footman(['cookies' => [
        'type' => 'jar',
        'strict' => true
    ]
]);

$res = $client->request(function ($request) {
    $request->request_type = 'GET';
    $request->request_url = 'https://github.com/login';
    $request->allow_redirects = true;
    $request->cookie_name = 'alshf89';
});

preg_match('/input.*?authenticity_token".*?value="(.*?)"/', $res->getContents(), $matches);
dump($matches[1]);

$res2 = $client->request(function ($request) use ($matches) {
    $request->request_type = 'POST';
    $request->request_url = 'https://github.com/session';
    $request->allow_redirects = true;
    $request->cookie_name = 'alshf89';
    $request->form_params = [
        'login' => 'alshf89',
        'password' => 'nima491010',
        'commit' => 'Sign in',
        'utf8' => '✓',
        'authenticity_token' => $matches[1],
    ];
});
dump($res2->getContents());
die;

// $user = 'alshf89';
// $cookie = new \GuzzleHttp\Cookie\FileCookieJar('/Users/alishafiee/Sites/footman/tests/' . md5($user), true);
// $cookie = new \GuzzleHttp\Cookie\CookieJar;
// $client = new \GuzzleHttp\Client(['cookies' => true]);

// $res = $client->request('GET', 'https://github.com/login', [
//     'cookies' => $cookie,
//     'allow_redirects' => true,
// ]);

// preg_match('/input.*?authenticity_token".*?value="(.*?)"/', $res->getBody()->getContents(), $matches);

// dump($cookie);
// dump($matches[1]);

// $res2 = $client->request('POST', 'https://github.com/session', [
//     'cookies' => $cookie,
//     'allow_redirects' => true,
//     'form_params' => [
//         'login' => $user,
//         'password' => '',
//         'commit' => 'Sign in',
//         'utf8' => '✓',
//         'authenticity_token' => $matches[1],
//     ],
// ]);

// $res2 = $client->request('GET', 'https://github.com/' . $user, [
//     'cookies' => $cookie
// ]);

// dump($res2->getBody()->getContents());
// dump($cookie);
