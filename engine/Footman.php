<?php

namespace Alshf;

use Closure;
use Alshf\Response;
use Alshf\Container;
use GuzzleHttp\Client;
use ReflectionException;
use GuzzleHttp\Exception\RequestException;
use Alshf\Exceptions\FootmanCookiesException;
use Alshf\Exceptions\FootmanRequestException;
use Alshf\Exceptions\FootmanBadRequestException;

class Footman
{
    /**
     * Contains request Options
     *
     * @var Illuminate/Support/Collection
     */
    private $options;

    /**
     * GuzzleHttp Client Object
     *
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * GuzzleHttp Response Object
     *
     * @var Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * Contains request Cookies Options
     *
     * @var Illuminate/Support/Collection
     */
    private $cookies;

    /**
     * Valid request Types
     * @var array
     */
    private static $requestType = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

    /**
     * Footman Constructor
     *
     * @param array $options
     * @return void
     */
    public function __construct(array $options = [])
    {
        // Set Options & Cookies Properties
        $this->setOptions($options);

        // Create New Instance of GuzzleHttp/Client
        $this->client = new Client(['cookies' => $this->canShareCookies()]);
    }

    /**
     * Footman getter
     *
     * @param  string $key
     * @return mix
     */
    public function __get($key)
    {
        return $this->options->get($key);
    }

    /**
     * Footman setter
     *
     * @param string $key
     * @param mix $value
     * @return void
     */
    public function __set($key, $value)
    {
        // Check the value is an array and the requested key on
        // Options Collection is array too
        if ($this->overwrite($key, $value)) {
            $value = collect($this->options->get($key))->merge($value);
        }

        $this->options->put($key, $value);
    }

    /**
     * Send Request on URL
     *
     * @param  Closure $closure
     * @return Alshf\Response
     */
    public function request(Closure $closure = null)
    {
        return $this->execute($closure)
                    ->checkRequestType()
                    ->checkURL()
                    ->make();
    }

    /**
     * Check request closure is callable
     * If it is then Execute it
     *
     * @param  closure $closure
     * @return Alshf\Footman
     */
    private function execute($closure)
    {
        if (is_callable($closure)) {
            $closure($this);
        }

        return $this;
    }

    /**
     * Check request has a request type
     *
     * @return Alshf\Footman
     */
    private function checkRequestType()
    {
        if (! $this->options->has('request_type')) {
            throw new FootmanBadRequestException('Please enter value for "request_type" in request closure.', 1001);
        }

        if (! $this->options->whereIn('request_type', static::$requestType)) {
            throw new FootmanBadRequestException('Invalid "request_type" [' . $this->options->get('request_type') . '].', 1002);
        }
        
        return $this;
    }

    /**
     * Check request has a request URL
     *
     * @return Alshf\Footman
     */
    private function checkURL()
    {
        if (! $this->options->has('request_url')) {
            throw new FootmanBadRequestException('Please enter value for "request_url" in request closure.', 1003);
        }

        return $this;
    }

    /**
     * Make Request on URL
     *
     * @return Alshf\Response
     */
    private function make()
    {
        try {
            // this Method Create Cookies Object Base on What we neeed
            // and then Implements created Object into GuzzleHttp\Client Request
            $this->setCookiesObject();

            // Send Request with Guzzle with Options
            $this->response = $this->client->request(
                $this->options->pull('request_type'),
                $this->options->pull('request_url'),
                $this->options->toArray()
            );
            
            // Clear Options from cookies & form data & multi part
            // and Make Options Property Clear for next request
            // with same Object [Alshf\Footman]
            $this->options->forget('form_params');
            $this->options->forget('multipart');
            $this->options->forget('cookies');

            return new Response($this->response);
        } catch (ReflectionException $e) {
            throw new FootmanCookiesException($e->getMessage(), 1004, $e);
        } catch (RequestException $e) {
            throw new FootmanRequestException($e);
        }
    }

    /**
     * Check Option property key with array value must be overwrite
     *
     * @param  string $key
     * @param  mix $value
     * @return bool
     */
    private function overwrite($key, $value)
    {
        return $this->options->has($key) && is_array($this->options->get($key)) && is_array($value);
    }

    /**
     * Check Cookie property has a share key
     *
     * @return bool
     */
    private function canShareCookies()
    {
        return $this->cookies->get('share') ? true : false;
    }

    /**
     * Check Cookie Property not empty
     *
     * @return bool
     */
    private function canUseCookies()
    {
        return !$this->cookies->isEmpty();
    }

    /**
     * Set request Options into Options Property
     *
     * @param array $options
     * @return void
     */
    private function setOptions(array $options)
    {
        $this->options = collect($options);

        // Pull Cookies Options from Options property and
        // Set Cookies Property
        $this->cookies = $this->setCookiesOptions();
    }

    /**
     * Set Cookies Options into Cookies Property
     *
     * @return void
     */
    private function setCookiesOptions()
    {
        $cookies = $this->options->pull('cookies');

        if (is_array($cookies)) {
            return collect($cookies);
        }

        // Default array for Cookies Property
        // if true pass throught constructor
        if ($cookies) {
            return collect([
                'share' => false,
                'type' => 'jar',
                'strict' => false
            ]);
        }

        return collect([]);
    }

    /**
     * Get Cookies Type if any type Available
     *
     * @return string
     */
    private function getCookiesType()
    {
        switch ($this->cookies->get('type')) {
            case 'file':
                return \GuzzleHttp\Cookie\FileCookieJar::class;
                break;

            case 'jar':
                return \GuzzleHttp\Cookie\CookieJar::class;
                break;
            
            default:
                throw new FootmanCookiesException('Invalid cookie "type" [' . $this->cookies->get('type') . '].', 1005);
                break;
        }
    }

    /**
     * Create Cookies Object or get it from previous request
     *
     * @return void
     */
    private function setCookiesObject()
    {
        // Check Cookies property has Value
        if ($this->canUseCookies()) {
            try {
                // Try to get Cookies Object From previous Request
                // This will throw Exception if no cookies object found
                $object = $this->getSingletonCookiesObject();
            } catch (FootmanCookiesException $e) {
                // Check if the cookies type is file then set
                // Cookie tag & cookie File into cookies property
                $this->handleFileCookies();

                $contianer = new Container(
                    $this->getCookiesType(),
                    $this->getCookiesObjectArguments()
                );

                // Create new Cookie Object
                $object = $contianer->make();

                // put Cookies Object into Cookies Property
                $this->cookies->put('object', $object);
            } finally {
                // put Cookies Object into Request Options Property
                $this->options->put('cookies', $object);
            }
        }
    }

    /**
     * Get cookies Object from Cookies Property
     *
     * @return GuzzleHttp\Cookie\CookieJarInterface
     */
    private function getSingletonCookiesObject()
    {
        if (!$this->cookies->has('object') ||
            $this->cookies->has('tag') &&
            !$this->compareCookiesTag()
        ) {
            throw new FootmanCookiesException;
        }

        return $this->cookies->get('object');
    }

    /**
     * Set Cookie Tag & Cookie File for Request
     * Into Cookies Property
     *
     * @return void
     */
    private function handleFileCookies()
    {
        if ($this->cookies->get('type') == 'file') {
            if (!$this->options->get('cookies_name')) {
                throw new FootmanCookiesException('Please enter value for "cookie_name" in request closure.', 1006);
            }

            $this->cookies->put('tag', $this->cookiesTag());
            $this->cookies->put('cookie_file', __DIR__ . '/Cookies/' . $this->cookiesTag());
        }
    }

    /**
     * Create Cookie Tag for cookie name
     *
     * @return string
     */
    private function cookiesTag()
    {
        return md5($this->options->get('cookies_name'));
    }

    /**
     * Compare Cookie Tag in Cookies Property with Cookie name request
     *
     * @return bool
     */
    private function compareCookiesTag()
    {
        return $this->cookies->get('tag') == $this->cookiesTag();
    }

    /**
     * Reindex with camel case the Cookies Property when we
     * Want to create Cookies Object with Container
     *
     * @return array
     */
    private function getCookiesObjectArguments()
    {
        return $this->cookies->mapWithKeys(function ($item, $key) {
            return [camel_case($key) => $item];
        })->toArray();
    }
}
