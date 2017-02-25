<?php

namespace Alshf;

use Closure;
use Alshf\Response;
use GuzzleHttp\Client;
use Alshf\Exceptions\FootmanException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7 as GuzzleErrorHandler;

class Footman
{
    private $options;

    private $client;

    private $response;

    private $cookies;

    private static $requestType = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

    public function __construct(array $options = [])
    {
        $this->setOptions($options);

        $this->client = new Client(['cookie' => $this->canShareCookies()]);
    }

    public function __get($key)
    {
        return $this->options->get($key);
    }

    public function __set($key, $value)
    {
        if ($this->overwrite($key, $value)) {
            $value = collect($this->options->get($key))->merge($value);
        }

        $this->options->put($key, $value);
    }

    public function request(Closure $closure = null)
    {
        return $this->execute($closure)
                    ->checkRequestType()
                    ->checkURL()
                    ->make();
    }

    private function execute($closure)
    {
        if (is_callable($closure)) {
            $closure($this);
        }

        return $this;
    }

    private function checkRequestType()
    {
        if (! $this->options->has('request_type')) {
            throw new FootmanException('No request type provided!');
        }

        if (! $this->options->whereIn('request_type', static::$requestType)) {
            throw new FootmanException('Invalid Request type [' . $this->options->get('request_type') . ']');
        }
        
        return $this;
    }

    private function checkURL()
    {
        if (! $this->options->has('request_url')) {
            throw new FootmanException('No request URL provided!');
        }

        return $this;
    }

    private function make()
    {
        $this->setCookieObject();

        try {
            $this->response = $this->client->request(
                $this->options->pull('request_type'),
                $this->options->pull('request_url'),
                $this->options->toArray()
            );
            
            $this->options->forget('form_params');
            $this->options->forget('multipart');

            return new Response($this->response);
        } catch (RequestException $e) {
            throw new FootmanException('Networking Errors : ' . $this->exceptionMessage($e));
        } catch (ClientException $e) {
            throw new FootmanException('Http Errors : ' . $this->exceptionMessage($e));
        }
    }

    private function exceptionMessage($exception)
    {
        $request = GuzzleErrorHandler\str($exception->getRequest());

        if ($exception->hasResponse()) {
            $response = GuzzleErrorHandler\str($exception->getResponse());
        }

        return collect(compact('request', 'response'))->implode(null);
    }

    private function overwrite($key, $value)
    {
        return $this->options->has($key) && is_array($this->options->get($key)) && is_array($value);
    }

    private function setOptions(array $options)
    {
        $this->options = collect($options);

        $this->cookies = $this->setCookiesOptions();
    }

    private function setCookiesOptions()
    {
        $cookies = $this->options->pull('cookies');

        if (is_array($cookies)) {
            return collect($cookies);
        }

        if ($cookies) {
            return collect([
                'share' => true,
                'type' => \GuzzleHttp\Cookie\CookieJar::class,
                'strict' => false
            ]);
        }

        return collect([]);
    }

    private function canShareCookies()
    {
        return $this->cookies->get('share') ? true : false;
    }

    private function canUseCookies()
    {
        return !$this->cookies->isEmpty();
    }

    private function getCookieType()
    {
        if (preg_match('/^.+\\\(File)*CookieJar$/', $this->cookies->get('type'))) {
            return $this->cookies->get('type');
        }

        throw new FootmanException('Invalid Cookie type [' . $this->cookies->get('type') . ']');
    }

    private function setCookieObject()
    {
        if ($this->cookies->has('object')) {
            if ($this->compareCookieTag()) {
                $this->options->put('cookies', $this->cookies->get('object'));
            }

            if (! $this->cookies->has('tag')) {
                $this->options->put('cookies', $this->cookies->get('object'));
            }
        }

        if (! $this->options->has('cookies')) {
            $type = $this->getCookieType();

            if ($type === \GuzzleHttp\Cookie\CookieJar::class) {
                if ($this->cookies->has('strict')) {
                    $cookies = new $type($this->cookies->get('strict') ? true : false);
                } else {
                    $cookies = new $type;
                }

                $this->options->put('cookies', $cookies);
            }

            if ($type === \GuzzleHttp\Cookie\FileCookieJar::class) {
                if (! $this->options->get('cookie_name')) {
                    throw new FootmanException('No Cookie name Provided!');
                }

                if ($this->cookies->has('session')) {
                    $cookies = new $type(__DIR__ . '/Cookies/' . $this->cookieTag() , $this->cookies->get('session') ? true : false);
                } else {
                    $cookies = new $type(__DIR__ . '/Cookies/' . $this->cookieTag());
                }

                $this->options->put('cookies', $cookies);
            }
        }
    }

    private function cookieTag()
    {
        return md5($this->options->get('request_url') . $this->options->get('cookie_name'));
    }

    private function compareCookieTag()
    {
        return $this->cookies->get('tag') === $this->cookieTag();
    }
}
