<?php

namespace PradApp\Response;

abstract class HttpCode
{

	const OK = 200;
	const CREATED = 201;
	const NO_CONTENT = 204;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const INTERNAL_SERVER_ERROR = 500;

}