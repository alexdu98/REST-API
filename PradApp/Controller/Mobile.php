<?php

namespace PradApp\Controller;

use PradApp\Response\HttpCode;
use PradApp\Response\Response;

class Mobile extends Controller
{

	public function getMobiles()
	{
		$req = $this->db->prepare('
			SELECT id, owner, version
			FROM mobile
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

	public function getMobileById($id)
	{
		$req = $this->db->prepare('
			SELECT id, owner, version
			FROM mobile 
			WHERE id = :id
		');

		try{
			$success = $req->execute(array(
				'id' => $id,
			));
			$data = $req->fetchAll();
			$httpCode = HttpCode::OK;

			// S'il n'y a aucune ligne c'est que l'id du mobile n'existe pas, on retourne une erreur
			if(count($data) === 0){
				$success = false;
				$data = array(
					'code'    => 0,
					'message' => 'MobileNotFoundException',
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

	public function getApplicationsOfMobileId($id)
	{
		// Si l'id du mobile n'existe pas, on retourne l'erreur
		$response = $this->getMobileById($id);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('
			SELECT a.id, a.name, a.version, a.type
			FROM mobile m
			INNER JOIN application_mobile am ON am.id_mobile = m.id
			INNER JOIN application a ON a.id = am.id_application
			WHERE m.id = :id
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

	public function getApplicationIdOfMobileId($idm, $ida)
	{
		// Si l'id du mobile n'existe pas, on retourne l'erreur
		$response = $this->getMobileById($idm);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('
			SELECT a.id, a.name, a.version, a.type
			FROM application_mobile am
			INNER JOIN application a ON a.id = am.id_application
			WHERE am.id_mobile = :idm AND am.id_application = :ida
		');

		try{
			$success = $req->execute(array(
				'idm' => $idm,
				'ida' => $ida,
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

	public function postMobiles()
	{
		$req = $this->db->prepare('INSERT INTO mobile(owner, version) VALUES(:owner, :version)');

		try{
			$success = $req->execute(array(
				'owner'   => $_POST['owner'],
				'version' => $_POST['version'],
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

	public function postApplicationsOfMobileId($idm)
	{
		// Si l'id du mobile n'existe pas, on retourne l'erreur
		$response = $this->getMobileById($idm);
		if($response->isSuccess() === false){
			return $response;
		}

		// Si l'id de l'application n'existe pas, on retourne l'erreur
		$application = new Application();
		$response = $application->getApplicationById($_POST['ida']);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('INSERT INTO application_mobile(id_mobile, id_application) VALUES(:idm, :ida)');

		try{
			$success = $req->execute(array(
				'idm' => $idm,
				'ida' => $_POST['ida'],
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

	public function putMobileById($id)
	{
		// Si l'id du mobile n'existe pas, on retourne l'erreur
		$response = $this->getMobileById($id);
		if($response->isSuccess() === false){
			return $response;
		}

		parse_str(file_get_contents("php://input"), $params);

		$req = $this->db->prepare('UPDATE mobile SET owner = :owner, version = :version WHERE id = :id');

		try{
			$success = $req->execute(array(
				'id'      => $id,
				'owner'   => $params['owner'],
				'version' => $params['version'],
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

	public function deleteMobileById($id)
	{
		// Si l'id du mobile n'existe pas, on retourne l'erreur
		$response = $this->getMobileById($id);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('DELETE FROM mobile WHERE id = :id');

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

	public function deleteApplicationsOfMobileId($id)
	{
		// Si l'id du mobile n'existe pas, on retourne l'erreur
		$response = $this->getMobileById($id);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('DELETE FROM application_mobile WHERE id_mobile = :id');

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

	public function deleteApplicationIdOfMobileId($idm, $ida)
	{

		// Si l'id du mobile n'existe pas, on retourne l'erreur
		$response = $this->getMobileById($idm);
		if($response->isSuccess() === false){
			return $response;
		}

		$req = $this->db->prepare('DELETE FROM application_mobile WHERE id_mobile = :idm AND id_application = :ida');

		try{
			$success = $req->execute(array(
				'idm' => $idm,
				'ida' => $ida,
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