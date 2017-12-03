<?php

namespace PradApp\Response;

class Response
{

	private $success;
	private $httpCode;
	private $data;

	public function __construct(bool $success, array $data, int $httpCode = 200)
	{
		$this->success = $success;
		$this->data = $data;
		$this->httpCode = $httpCode;
	}

	public function isSuccess() : bool
	{
		return $this->success;
	}

	public function getHttpCode() : int
	{
		return $this->httpCode;
	}

	public function getData() : array
	{
		return $this->data;
	}

	public function send()
	{
		header("Content-type:application/json");
		http_response_code($this->httpCode);

		echo json_encode(array(
			'success' => $this->success,
			'data' => $this->data
		));
	}

}