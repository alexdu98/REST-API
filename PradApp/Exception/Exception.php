<?php

namespace PradApp\Exception;

use PradApp\Response\Response;

abstract class Exception extends \Exception
{

	protected $httpCode;

	public function __construct($message, $httpCode, $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->httpCode = $httpCode;
	}

	public function __toString()
	{
		return get_called_class() . ": {$this->message}\n";
	}

	public function getHttpCode()
	{
		return $this->httpCode;
	}

	public function send()
	{
		$data = array(
			'code' => $this->code,
			'message' => $this->message
		);
		$response = new Response(false, $data, $this->httpCode);
		$response->send();
	}

}