<?php

namespace PradApp\Controller;

use PradApp\DB\DB;

abstract class Controller
{

	protected $db;
	protected $response;

	public function __construct()
	{
		// Obtention de l'instance PDO
		$this->db = DB::getInstancePDO();
	}

}