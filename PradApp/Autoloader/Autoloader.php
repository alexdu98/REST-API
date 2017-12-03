<?php

namespace PradApp\Autoloader;

class Autoloader
{

	private static $path;

	public static function register($path = '')
	{
		self::$path = $path;

		spl_autoload_register(array(
			__CLASS__,
			'autoload',
		));
	}

	public static function autoload($class)
	{
		$file = self::$path . str_replace('\\', '/', $class) . '.php';
		require $file;
	}

}