<?php

namespace Alshf;

use Psr\Http\Message\ResponseInterface;
use Alshf\Exceptions\FootmanResponseException;

class Response
{
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function read($length)
    {
        return $this->getBody()->read($length);
    }

    public function getContents()
    {
        return $this->getBody()->getContents();
    }

    public function seek($pointer)
    {
        return $this->getBody()->seek($pointer);
    }

    public function getHeaders()
    {
        return collect($this->getHeaders());
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->response, $method)) {
            return call_user_func_array([$this->response, $method], $arguments);
        }

        throw new FootmanResponseException('Method [' . $method . '] doesn\'t exist!');
    }
}
