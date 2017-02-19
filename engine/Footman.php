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
    private $parameters;

    private $client;

    private $response;

    private static $requestType = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    public function __construct(array $parameters = [])
    {
        $this->client = new Client;

        $this->parameters = collect($parameters);
    }

    public function __get($key)
    {
        return $this->parameters->get($key);
    }

    public function __set($key, $value)
    {
        $this->parameters->put($key, $value);
    }

    public function request(Closure $closure)
    {
        return $this->execute($closure)
                    ->checkRequestType()
                    ->checkURL()
                    ->make();
    }

    private function execute(Closure $closure)
    {
        if (! is_callable($closure)) {
            throw new FootmanException('No Closure Found to execute!');
        }
        
        $closure($this);

        return $this;
    }

    private function checkRequestType()
    {
        if (! $this->parameters->has('request_type')) {
            throw new FootmanException('No request type provided!');
        }

        if (! $this->parameters->whereIn('request_type', static::$requestType)) {
            throw new FootmanException('Invalid Request type [' . $this->parameters->get('request_type') . ']');
        }
        
        return $this;
    }

    private function checkURL()
    {
        if (! $this->parameters->has('request_url')) {
            throw new FootmanException('No request URL provided!');
        }

        return $this;
    }

    private function make()
    {
        try {
            $this->response = $this->client->request(
                $this->parameters->pull('request_type'),
                $this->parameters->pull('request_url'),
                $this->parameters->toArray()
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
}
