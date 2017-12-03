<?php

namespace PradApp\Controller;

use PradApp\Response\HttpCode;
use PradApp\Response\Response;

class Application extends Controller
{

	public function getApplications()
	{
		$req = $this->db->prepare('
			SELECT id, name, version, type
			FROM application
		');

		try{
			$success = $req->execute();
			$data = $req->fetchAll();
			$httpCode = HttpCode::OK;
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function getApplicationById($id)
	{
		$req = $this->db->prepare('
			SELECT id, name, version, type
			FROM application 
			WHERE id = :id
		');

		try{
			$success = $req->execute(array(
				'id' => $id,
			));
			$data = $req->fetchAll();
			$httpCode = HttpCode::OK;

			// S'il n'y a aucune ligne c'est que l'id de l'application n'existe pas, on retourne une erreur
			if(count($data) === 0){
				$success = false;
				$data = array(
					'code'    => 0,
					'message' => 'ApplicationNotFoundException',
				);
				$httpCode = HttpCode::NOT_FOUND;
			}
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function getMobilesOfApplicationId($id)
	{
		// Si l'id de l'application n'existe pas, on retourne l'erreur
		$response = $this->getApplicationById($id);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('
			SELECT m.id, m.owner, m.version
			FROM application a
			INNER JOIN application_mobile am ON am.id_application = a.id
			INNER JOIN mobile m ON m.id = am.id_mobile
			WHERE a.id = :id
		');

		try{
			$success = $req->execute(array(
				'id' => $id,
			));
			$data = $req->fetchAll();
			$httpCode = HttpCode::OK;
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function getMobileIdOfApplicationId($ida, $idm)
	{
		// Si l'id de l'application n'existe pas, on retourne l'erreur
		$response = $this->getApplicationById($ida);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('
			SELECT m.id, m.owner, m.version
			FROM application_mobile am
			INNER JOIN mobile m ON m.id = am.id_mobile
			WHERE am.id_application = :ida AND am.id_mobile = :idm
		');

		try{
			$success = $req->execute(array(
				'ida' => $ida,
				'idm' => $idm,
			));
			$data = $req->fetchAll();
			$httpCode = HttpCode::OK;
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function postApplications()
	{

		$req = $this->db->prepare('INSERT INTO application(name, version, type) VALUES(:name, :version, :type)');

		try{
			$success = $req->execute(array(
				'name'    => $_POST['name'],
				'version' => $_POST['version'],
				'type'    => $_POST['type'],
			));
			$data = array(
				'id' => $this->db->lastInsertId(),
			);
			$httpCode = HttpCode::CREATED;
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function postMobilesOfApplicationId($ida)
	{
		// Si l'id de l'application n'existe pas, on retourne l'erreur
		$response = $this->getApplicationById($ida);
		if($response->isSuccess() === false){
			return $response;
		}

		// Si l'id du mobile n'existe pas, on retourne l'erreur
		$mobile = new Mobile();
		$response = $mobile->getMobileById($_POST['idm']);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('INSERT INTO application_mobile(id_application, id_mobile) VALUES(:ida, :idm)');

		try{
			$success = $req->execute(array(
				'ida' => $ida,
				'idm' => $_POST['idm'],
			));
			$data = array(
				'id' => $this->db->lastInsertId(),
			);
			$httpCode = HttpCode::CREATED;
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function putApplicationById($id)
	{
		// Si l'id de l'application n'existe pas, on retourne l'erreur
		$response = $this->getApplicationById($id);
		if($response->isSuccess() === false){
			return $response;
		}

		parse_str(file_get_contents("php://input"), $params);

		$req = $this->db->prepare('UPDATE application SET name = :name, version = :version, type = :type WHERE id = :id');

		try{
			$success = $req->execute(array(
				'id'      => $id,
				'name'    => $params['name'],
				'version' => $params['version'],
				'type'    => $params['type'],
			));
			$data = array();
			$httpCode = HttpCode::NO_CONTENT;
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function deleteApplicationById($id)
	{
		// Si l'id de l'application n'existe pas, on retourne l'erreur
		$response = $this->getApplicationById($id);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('DELETE FROM application WHERE id = :id');

		try{
			$success = $req->execute(array(
				'id' => $id,
			));
			$data = array();
			$httpCode = HttpCode::NO_CONTENT;
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function deleteMobilesOfApplicationId($id)
	{
		// Si l'id de l'application n'existe pas, on retourne l'erreur
		$response = $this->getApplicationById($id);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('DELETE FROM application_mobile WHERE id_application = :id');

		try{
			$success = $req->execute(array(
				'id' => $id,
			));
			$data = array();
			$httpCode = HttpCode::NO_CONTENT;
		}
		catch(\Exception $e){
			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

	public function deleteMobileIdOfApplicationId($ida, $idm)
	{
		// Si l'id de l'application n'existe pas, on retourne l'erreur
		$response = $this->getApplicationById($ida);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('DELETE FROM application_mobile WHERE id_application = :ida AND id_mobile = :idm');

		try{
			$success = $req->execute(array(
				'ida' => $ida,
				'idm' => $idm,
			));
			$data = array();
			$httpCode = HttpCode::NO_CONTENT;
		}
		catch(\Exception $e){

			$success = false;
			$data = array(
				'code'    => $e->getCode(),
				'message' => get_class($e),
			);
			$httpCode = HttpCode::INTERNAL_SERVER_ERROR;
		}

		return new Response($success, $data, $httpCode);
	}

}