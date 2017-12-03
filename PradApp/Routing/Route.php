<?php

namespace PradApp\Routing;

use ReflectionMethod;

class Route
{

	private $url;
	private $controller;
	private $method;
	private $matches;

	public function __construct($url, $controller, $method)
	{
		$this->url = trim($url, '/');
		$this->controller = $controller;
		$this->method = $method;
	}

	public function match($url)
	{
		$url = trim($url, '/');
		// On remplace les :nombre par une regex
		$pattern = preg_replace('#:([\w]+)#', '([^/]+)', $this->url);

		// Si l'url ne correspond pas à la route
		if(!preg_match('#^' . $pattern . '$#', $url, $this->matches)){
			return false;
		}

		// On supprime le premier élément qui correspond à la chaine complète
		array_shift($this->matches);

		return true;
	}

	public function call()
	{
		// Récupère des informations sur controller::method()
		$reflectionMethod = new ReflectionMethod($this->controller, $this->method);

		// Invoque method avec comme argument matches
		return $reflectionMethod->invokeArgs(new $this->controller(), $this->matches);
	}

}