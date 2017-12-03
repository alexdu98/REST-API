<?php

require '../PradApp/Autoloader/Autoloader.php';
PradApp\Autoloader\Autoloader::register('../');

use PradApp\Request\Request;


// ########################
// TRAITEMENT DE FORMULAIRE
// ########################
define('API', 'http://' . $_SERVER['SERVER_NAME'] . '/API');
$response = null;
$responseText = '';
if(!empty($_POST))
{
	if(isset($_POST['deleteMobile']) && !empty($_POST['id']))
	{
		$response = Request::delete(API . '/mobile/' . $_POST['id']);
	}
	elseif(isset($_POST['deleteApplication']) && !empty($_POST['id']))
	{
		$response = Request::delete(API . '/application/' . $_POST['id']);
	}
	elseif(isset($_POST['addMobile']) && !empty($_POST['owner']) && !empty($_POST['version']))
	{
		unset($_POST['addMobile']);
		$response = Request::post(API . '/mobiles', http_build_query($_POST));
	}
	elseif(isset($_POST['addApplication']) && !empty($_POST['name']) && !empty($_POST['version']) && !empty($_POST['type']))
	{
		unset($_POST['addApplication']);
		$response = Request::post(API . '/applications', http_build_query($_POST));
	}
	elseif(isset($_POST['modifyMobile']) && !empty($_POST['id']) && !empty($_POST['owner']) && !empty($_POST['version']))
	{
		$id = $_POST['id'];
		unset($_POST['modifyMobile'], $_POST['id']);
		$response = Request::put(API . '/mobile/' . $id, http_build_query($_POST));
	}
	elseif(isset($_POST['modifyApplication']) && !empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['version']) && !empty($_POST['type']))
	{
		$id = $_POST['id'];
		unset($_POST['modifyApplication'], $_POST['id']);
		$response = Request::put(API . '/application/' . $id, http_build_query($_POST));
	}
	else{
		$response = (object) array(
			'success' => false,
			'data' => (object) array('message' => 'Les paramètres de la requête sont invalides')
		);
	}

	if($response->success)
		$responseText .= 'Requête traitée avec succès<br>';
	else
		$responseText .= '<b>Erreur lors de la requête : ' . $response->data->message . '</b><br>';
}


// ######################
// CHARGEMENT DES DONNEES
// ######################
$mobilesResponse = Request::get(API . '/mobiles');
$applicationsResponse = Request::get(API . '/applications');

$mobiles = array();
if($mobilesResponse->success)
	$mobiles = $mobilesResponse->data;
else
	$responseText .= '<b>Erreur lors de la récupération des mobiles : ' . $mobilesResponse->data->message . '</b><br>';

$applications = array();
if($applicationsResponse->success)
	$applications = $applicationsResponse->data;
else
	$responseText .= '<b>Erreur lors de la récupération des applications : ' . $applicationsResponse->data->message . '</b><br>';

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Interface web</title>
</head>
<body>

	<?php if(!empty($responseText)): ?>
		<p><?= $responseText; ?></p>
	<?php endif; ?>

	<h2>Liste des mobiles</h2>
	<table border>
		<tr>
			<th>Id</th>
			<th>Owner</th>
			<th>Version</th>
			<th>Action</th>
		</tr>
		<?php foreach ($mobiles as $mobile): ?>
			<tr>
				<form action="#" method="post">
					<td><input type="text" name="id" value="<?= $mobile->id; ?>" readonly required></td>
					<td><input type="text" name="owner" value="<?= $mobile->owner; ?>" required></td>
					<td><input type="text" name="version" value="<?= $mobile->version; ?>" required></td>
					<td>
						<a href="get.php?controller=mobile&id=<?= $mobile->id; ?>">voir</a>
						<input type="submit" name="modifyMobile" value="modifier">
						<input type="submit" name="deleteMobile" value="supprimer">
					</td>
				</form>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td></td>
			<form action="#" method="post">
				<td><input type="text" name="owner" required></td>
				<td><input type="text" name="version" required></td>
				<td><input type="submit" name="addMobile" value="ajouter"></td>
			</form>
		</tr>
	</table>

	<h2>Liste des applications</h2>
	<table border>
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>Version</th>
			<th>Type</th>
			<th>Action</th>
		</tr>
		<?php foreach ($applications as $application): ?>
			<tr>
				<form action="#" method="post">
					<td><input type="text" name="id" value="<?= $application->id; ?>" readonly required></td>
					<td><input type="text" name="name" value="<?= $application->name; ?>" required></td>
					<td><input type="text" name="version" value="<?= $application->version; ?>" required></td>
					<td><input type="text" name="type" value="<?= $application->type; ?>" required></td>
					<td>
						<a href="get.php?controller=application&id=<?= $application->id; ?>">voir</a>
						<input type="submit" name="modifyApplication" value="modifier">
						<input type="submit" name="deleteApplication" value="supprimer">
					</td>
				</form>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td></td>
			<form action="#" method="post">
				<td><input type="text" name="name" required></td>
				<td><input type="text" name="version" required></td>
				<td><input type="text" name="type" required></td>
				<td><input type="submit" name="addApplication" value="ajouter"></td>
			</form>
		</tr>
	</table>

</body>
</html>