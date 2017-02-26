<?php

namespace Alshf;

use Closure;
use Alshf\Response;
use Alshf\Container;
use GuzzleHttp\Client;
use ReflectionException;
use Alshf\Exceptions\FootmanException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7 as GuzzleErrorHandler;
use Alshf\Exceptions\FootmanCookiesException;
use Alshf\Exceptions\FootmanRequestException;

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

        $this->client = new Client(['cookies' => $this->canShareCookies()]);
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
            throw new FootmanRequestException('No request type provided!');
        }

        if (! $this->options->whereIn('request_type', static::$requestType)) {
            throw new FootmanRequestException('Invalid Request type [' . $this->options->get('request_type') . ']');
        }
        
        return $this;
    }

    private function checkURL()
    {
        if (! $this->options->has('request_url')) {
            throw new FootmanRequestException('No request URL provided!');
        }

        return $this;
    }

    private function make()
    {
        try {
            $this->setCookiesObject();

            $this->response = $this->client->request(
                $this->options->pull('request_type'),
                $this->options->pull('request_url'),
                $this->options->toArray()
            );
            
            $this->options->forget('form_params');
            $this->options->forget('multipart');
            $this->options->forget('cookies');

            return new Response($this->response);
        } catch (ReflectionException $e) {
            throw new FootmanCookiesException('Cookies Container : ' . $e->getMessage());
        } catch (RequestException $e) {
            throw new FootmanRequestException('Networking Errors : ' . $this->exceptionMessage($e));
        } catch (ClientException $e) {
            throw new FootmanRequestException('Http Errors : ' . $this->exceptionMessage($e));
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

    private function canShareCookies()
    {
        return $this->cookies->get('share') ? true : false;
    }

    private function canUseCookies()
    {
        return !$this->cookies->isEmpty();
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
                'share' => false,
                'type' => 'jar',
                'strict' => false
            ]);
        }

        return collect([]);
    }

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
                throw new FootmanCookiesException('Invalid Cookie type [' . $this->cookies->get('type') . ']');
                break;
        }
    }

    private function setCookiesObject()
    {
        if ($this->canUseCookies()) {
            try {
                $object = $this->getSingletonCookiesObject();
            } catch (FootmanException $e) {
                $this->handleFileCookies();

                $contianer = new Container(
                    $this->getCookiesType(),
                    $this->getCookiesObjectArguments()
                );

                $object = $contianer->make();

                $this->cookies->put('object', $object);
            } finally {
                $this->options->put('cookies', $object);
            }
        }
    }

    private function getSingletonCookiesObject()
    {
        if (!$this->cookies->has('object') ||
            $this->cookies->has('tag') &&
            !$this->compareCookiesTag()
        ) {
            throw new FootmanException;
        }

        return $this->cookies->get('object');
    }

    private function handleFileCookies()
    {
        if ($this->cookies->get('type') == 'file') {
            if (!$this->options->get('cookies_name')) {
                throw new FootmanCookiesException('No Cookies name Provided!');
            }

            $cookiesTag = $this->cookiesTag();
            
            $this->cookies->put('tag', $cookiesTag);
            $this->cookies->put('cookie_file', __DIR__ . '/Cookies/' . $cookiesTag);
        }
    }

    private function cookiesTag()
    {
        return $this->options->get('cookies_name');
    }

    private function compareCookiesTag()
    {
        return $this->cookies->get('tag') == $this->cookiesTag();
    }

    private function getCookiesObjectArguments()
    {
        return $this->cookies->mapWithKeys(function ($item, $key) {
            return [camel_case($key) => $item];
        })->toArray();
    }
}
