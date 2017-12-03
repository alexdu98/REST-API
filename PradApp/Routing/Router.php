<?php

namespace PradApp\Routing;

use PradApp\Exception\RouteNotFoundException;
use PradApp\Exception\EmptyRequestMethodException;
use PradApp\Exception\UnsupportedRequestMethodException;
use PradApp\Response\HttpCode;

class Router{

	private $url;
	private $routes;
	private $methods = ['GET', 'POST', 'PUT', 'DELETE'];

	public function __construct($url)
	{
		$this->url = $url;
	}

	public function get($url, $controller, $method)
	{
		$route = new Route($url, $controller, $method);
		$this->routes['GET'][] = $route;
	}

	public function post($url, $controller, $method)
	{
		$route = new Route($url, $controller, $method);
		$this->routes['POST'][] = $route;
	}

	public function put($url, $controller, $method)
	{
		$route = new Route($url, $controller, $method);
		$this->routes['PUT'][] = $route;
	}

	public function delete($url, $controller, $method)
	{
		$route = new Route($url, $controller, $method);
		$this->routes['DELETE'][] = $route;
	}

	public function run()
	{
		if(empty($_SERVER['REQUEST_METHOD']))
		{
			throw new EmptyRequestMethodException('Request method needed', HttpCode::METHOD_NOT_ALLOWED);
		}
		elseif(!in_array($_SERVER['REQUEST_METHOD'], $this->methods))
		{
			throw new UnsupportedRequestMethodException('Request method unsupported (' . $_SERVER['REQUEST_METHOD'] .')', HttpCode::METHOD_NOT_ALLOWED);
		}

		foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route)
		{
			if($route->match($this->url))
			{
				return $route->call();
			}
		}

		throw new RouteNotFoundException('Route not found (' . $this->url . ')', HttpCode::NOT_FOUND);
	}

}