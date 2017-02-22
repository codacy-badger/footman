<?php

require dirname(__DIR__) . '/vendor/autoload.php';

// use Alshf\Footman;
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


$user = 'alshf89';
// $cookie = new \GuzzleHttp\Cookie\FileCookieJar('c:/xampp/htdocs/footman/tests/' . md5($user), true);
$cookie = new \GuzzleHttp\Cookie\CookieJar;
$client = new \GuzzleHttp\Client;

$res = $client->request('GET', 'https://github.com/login', [
    'cookies' => $cookie,
    'allow_redirects' => true,
    // 'synchronous' => true,
    // 'header' => [
    //     'Host' => 'github.com',
    //     'Connection' => 'keep-alive',
    //     'Accept-Language' => 'en-US,en;q=0.8',
    //     'Upgrade-Insecure-Requests' => 1,
    //     'Accept-Encoding' => 'gzip',
    //     'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'
    // ],
    // 'decode_content' => 'gzip'
]);

preg_match('/input.*?authenticity_token".*?value="(.*?)"/', $res->getBody()->getContents(), $matches);

dump($cookie);
dump($matches[1]);

// $client = new \GuzzleHttp\Client(['cookies' => true]);
// $cookie = new \GuzzleHttp\Cookie\FileCookieJar('c:/xampp/htdocs/footman/tests/' . md5($user), true);

$res2 = $client->request('POST', 'https://github.com/session', [
    'cookies' => $cookie,
    'allow_redirects' => true,
    // 'synchronous' => true,
    // 'header' => [
    //     'Host' => 'github.com',
    //     'Origin' => 'https://github.com',
    //     'Referer' => 'https://github.com/',
    //     'Accept-Encoding' => 'gzip',
    //     'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36'
    // ],
    // 'decode_content' => 'gzip',
    'form_params' => [
        'login' => $user,
        'password' => 'nima491010',
        'commit' => 'Sign in',
        'utf8' => '✓',
        'authenticity_token' => $matches[1],
        // 'authenticity_token' => '+LxqaKlOverBJkd22aYwnK4pL1HPxiAt2sKq6c45tQHCb1/2Beds9c0AkE1FzJweyyEtnbkQ2CUxLXsxg8Xpng==',
    ],
]);

// $res2 = $client->request('GET', 'https://github.com/' . $user, [
//     'cookies' => $cookie
// ]);

dump($res2->getBody()->getContents());
dump($cookie);
