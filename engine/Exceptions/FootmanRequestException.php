<?php

namespace Alshf\Exceptions;

use Alshf\Exceptions\FootmanException;
use GuzzleHttp\Exception\RequestException;

class FootmanRequestException extends FootmanException
{
	/**
	 * Contains Exception Response like Status Code, Reason Phrase and etc
	 *
	 * @var Psr\Http\Message\ResponseInterface
	 */
	private $response;

	/**
	 * Contains Exception request like method, uri, request target nad etc
	 *
	 * @var Psr\Http\Message\RequestInterface
	 */
	private $request;

	/**
	 * Exception Constructor
	 *
	 * @param RequestException $e
	 */
	public function __construct(RequestException $e) {
		$this->request = $e->getRequest();
		$this->response = $e->hasResponse() ? $e->getResponse() : null;

		parent::__construct($e->getMessage(), $e->getCode(), $e);
	}

	/**
	 * Create Summary from Exception
	 *
	 * @return string
	 */
	public function summary() {
		$request = \GuzzleHttp\Psr7\str($this->request);
		$response = $this->response ? \GuzzleHttp\Psr7\str($this->response) : null;

        return collect(compact('request', 'response'))->implode(null);
	}

	/**
	 * Call method on response or request property
	 *
	 * @param  string $method
	 * @param  string $arguments
	 * @return mix
	 */
	public function __call($method, $arguments) {
		if ($this->request && method_exists($this->request, $method)) {
			// Available Methods: getMethod, getRequestTarget, getUri
			return call_user_func_array([$this->request, $method], $arguments);
		}

		if ($this->response && method_exists($this->response, $method)) {
			// Available Methods: getStatusCode, getReasonPhrase
			return call_user_func_array([$this->response, $method], $arguments);
		}

		return null;
	}
}