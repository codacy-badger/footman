<?php

namespace Alshf;

use Closure;
use Alshf\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7 as GuzzleErrorHandler;
use Alshf\Exceptions\FootmanException;

class Footman
{
    private $options;

    private $client;

    private $response;

    private static $requestType = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

    public function __construct(array $options = [])
    {
        $this->options = collect($options);

        $this->client = new Client;
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
        try {
            $this->response = $this->client->request(
                $this->options->pull('request_type'),
                $this->options->pull('request_url'),
                $this->options->toArray()
            );

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
}
