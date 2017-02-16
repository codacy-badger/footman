<?php

namespace Alshf;

use Psr\Http\Message\ResponseInterface;

class Response
{
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getBody()
    {
        return $this->response->getBody();
    }

    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    public function getHeader($key)
    {
        if ($this->hasHeader($key)) {
            return $this->response->getHeader($key);
        }

        return null;
    }

    public function read($length)
    {
        return $this->getBody()->read($length);
    }

    public function getContents()
    {
        return $this->getBody()->getContents();
    }

    public function hasHeader($key)
    {
        return $this->response->hasHeader($key);
    }

    public function getStatus()
    {
        return $this->response->getStatusCode();
    }

    public function getStatusPhrase()
    {
        return $this->response->getReasonPhrase();
    }
}
