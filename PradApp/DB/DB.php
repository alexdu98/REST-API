<?php

namespace PradApp\DB;

use PDO;
use PDOException;

/**
 * Classe de connexion à la base de données
 * Design pattern Singleton pour n'ouvrir qu'une seule connexion
 */
class DB
{

	private static $pdo = null;
	private static $DB_HOST = "localhost";
	private static $DB_NAME = "pradeo";
	private static $DB_USER = "pradeo";
	private static $DB_PASS = "pradeo";

	static public function getInstancePDO()
	{
		if(self::$pdo == null){
			try{
				self::$pdo = new PDO('mysql:dbname=' . self::$DB_NAME . ';host=' . self::$DB_HOST, self::$DB_USER, self::$DB_PASS);
				self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); // le fetch() retourne des objets std par défaut
				self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Les erreurs sont traitées comme exception
				self::$pdo->exec("SET NAMES UTF8"); // Les échanges se font en UTF8
			}
			catch(PDOException $e){
				die('Problème de base de données');
			}
		}

		return self::$pdo;
	}

}