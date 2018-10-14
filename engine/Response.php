<?php

namespace Alshf;

use Psr\Http\Message\ResponseInterface;
use Alshf\Exceptions\FootmanResponseException;

class Response
{
    /**
     * Request Response Object
     *
     * @var Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * Footman Response Constructor
     *
     * @param Psr\Http\Message\ResponseInterface $response
     * @return void
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Read Number of Character from response body
     *
     * @param  int $length
     * @return string
     */
    public function read($length)
    {
        return $this->response->getBody()->read($length);
    }

    /**
     * Get all body Contents
     *
     * @return string
     */
    public function getContents()
    {
        return $this->response->getBody()->getContents();
    }

    /**
     * Rewind Body
     *
     * @param  int $pointer
     * @return string
     */
    public function seek($pointer)
    {
        return $this->response->getBody()->seek($pointer);
    }

    /**
     * Get all request header
     *
     * @return Illuminate\Support\Collection
     */
    public function getHeaders()
    {
        return collect($this->response->getHeaders());
    }

    /**
     * Call to all GuzzleHttp Response methods
     *
     * @param  string $method
     * @param  array $arguments
     * @return mix
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->response, $method)) {
            return call_user_func_array([$this->response, $method], $arguments);
        }

        throw new FootmanResponseException('Method [' . $method . '] doesn\'t exist.');
    }
}
