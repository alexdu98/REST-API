<?php

namespace PradApp\Request;

use PradApp\Response\HttpCode;

class Request
{

	public static function get($url)
	{
		return self::exec('get', $url);
	}

	public static function post($url, $parameters)
	{
		return self::exec('post', $url, $parameters);
	}

	public static function put($url, $parameters)
	{
		return self::exec('put', $url, $parameters);
	}

	public static function delete($url)
	{
		return self::exec('delete', $url);
	}

	public static function exec($method, $url, $parameters = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		switch($method){
			case 'get':
				break;

			case 'post':
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
				break;

			case 'put':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
				break;

			case 'delete':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
		}

		$res = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		// Si c'est différent de NO_CONTENT (204) alors on décode le contenu
		if($httpCode != HttpCode::NO_CONTENT)
			return json_decode($res);
		else
			return (object) array('success' => true);
	}

}