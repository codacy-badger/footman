<?php

use Alshf\Footman;
use Alshf\Response;
use PHPUnit\Framework\TestCase;
use Alshf\Exceptions\FootmanException;
use GuzzleHttp\Exception\RequestException;
use Alshf\Exceptions\FootmanCookiesException;
use Alshf\Exceptions\FootmanRequestException;
use Alshf\Exceptions\FootmanBadRequestException;

final class FootmanTest extends TestCase {

	/**
     * @dataProvider cookiesNameProvider
     */
	public function testCreateFileCookie($name) {
	    try {
	        $client = new Footman([
	            'cookies' => [
	                'share' => true,
	                // If you use [file] as a type you can
	                // Set store_session_cookies to true to store session cookies in the cookie jar.
	                // When you use File Cookie type you must pass cookie_name in request method closure
	                'type' => 'file',
	                'store_session_cookies' => true
	            ],
	            'header' => [
	                'User-Agent' => 'Footman CURL'
	            ],
	            'allow_redirects' => true,
	        ]);

	        $response = $client->request(function ($request) use ($name) {
	            $request->request_type = 'GET';
	            $request->request_url = 'https://github.com/';
	            $request->cookies_name = $name;
	        });

	        $this->assertInstanceOf(Response::class, $response);
	       	$this->assertEquals($response->getStatusCode(), 200);
	       	$this->assertEquals($response->getReasonPhrase(), 'OK');

	       	$this->assertGreaterThanOrEqual(0, $response->getHeaders()->count());
			$this->assertTrue($response->hasHeader('Content-Type'));

			$this->assertNotEmpty($response->getContents());
	       	$this->assertInternalType('string', $response->getContents());
	    } catch (FootmanException $e) {
	        // Catch All HTML & connection Errors, timeouts and etc...
	        $this->expectException(sprintf('%s => %s', get_class($e), $e->getMessage()));
	    }
	}

	/**
     * @depends testCreateFileCookie
	 * @dataProvider fileCookiesPathProvider
     */
	public function testCookieFileExist($path) {
        $this->assertFileExists($path);

        unlink($path);
	}

	public function testCookieJar() {
		try {
	        $client = new Footman([
	            'cookies' => [
	                'share' => true,
	                // If you use [jar] as a type you can
	                // Set strict_mode to true to throw exceptions when
	                // Invalid cookies are added to the cookie jar.
	                'type' => 'jar',
	                'strict_mode' => true
	            ]
	        ]);

	        $response = $client->request(function ($request) {
	            $request->request_type = 'GET';
	            $request->request_url = 'https://github.com/';
	        });

	       	$this->assertEquals($response->getStatusCode(), 200);
	       	$this->assertEquals($response->getReasonPhrase(), 'OK');
	    } catch (FootmanException $e) {
	        $this->expectException(sprintf('%s => %s', get_class($e), $e->getMessage()));
	    }
	}

	public function testLoginToGithubExample() {
		// Comment this line and enter your username and password
		$this->markTestSkipped();

		try {
	        $client = new Footman([
	            'cookies' => [
	                'share' => true,
	                'type' => 'jar',
	                'strict_mode' => true
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
	                'login' => 'Your Username',
	                'password' => 'Your Password',
	                'commit' => 'Sign in',
	                'utf8' => '✓',
	                'authenticity_token' => $matches[1],
	            ];
	        });

	        $this->assertInstanceOf(Response::class, $response);
	    } catch (FootmanException $e) {
	        // Catch All HTML & connection Errors, timeouts and etc...
	        $this->expectException(sprintf('%s => %s', get_class($e), $e->getMessage()));
	    }
	}

	public function testBadRequestException() {
		try {
		    $client = new Footman();

		    $client->request(function ($request) {
		        $request->request_type = 'GET';
		    });
		} catch (FootmanException $e) {
			$this->assertInstanceOf(FootmanBadRequestException::class, $e);
		}
	}

	public function testCookiesException() {
		try {
	        $client = new Footman([
	            'cookies' => [
	                'share' => true,
	                'type' => 'file',
	                'store_session_cookies' => true
	            ],
	            'allow_redirects' => true,
	        ]);

	        $client->request(function ($request) {
	            $request->request_type = 'GET';
	            $request->request_url = 'https://github.com/';
	        });
	    } catch (FootmanCookiesException $e) {
	        $this->assertInstanceOf(FootmanCookiesException::class, $e);
	    }
	}

	public function test404Exception() {
		try {
		    $client = new Footman();

		    $client->request(function ($request) {
		        $request->request_type = 'GET';
		        $request->request_url = 'https://httpstat.us/404';
		    });
		} catch (FootmanException $e) {
			$this->assertInstanceOf(FootmanRequestException::class, $e);
			$this->assertContains('Not Found', $e->getReasonPhrase());
			$this->assertEquals($e->getStatusCode(), 404);
		}
	}

	public function test500Exception() {
		try {
		    $client = new Footman();

		    $client->request(function ($request) {
		        $request->request_type = 'GET';
		        $request->request_url = 'https://httpstat.us/500';
		    });
		} catch (FootmanException $e) {
			$this->assertInstanceOf(FootmanRequestException::class, $e);
			$this->assertContains('Internal Server Error', $e->getReasonPhrase());
			$this->assertEquals($e->getStatusCode(), 500);
		}
	}

	public function fileCookiesPathProvider($name) {
		return [[dirname(__DIR__) . '/engine/Cookies/' . md5('footman_test')]];
	}

	public function cookiesNameProvider() {
		return [['footman_test']];
	}
}