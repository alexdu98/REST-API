<?php

require '../PradApp/Autoloader/Autoloader.php';
PradApp\Autoloader\Autoloader::register('../');

use PradApp\Request\Request;


// ######################
// CHARGEMENT DES DONNEES
// ######################
define('API', 'http://' . $_SERVER['SERVER_NAME'] . '/API');
$obj = null;
$relations = null;
$response = null;
$responseText = '';
$opposite = null;

if(!empty($_GET['controller']) && !empty($_GET['id']))
{
	$response = Request::get(API . '/' . $_GET['controller'] . '/' . $_GET['id']);

	if($response->success)
	{
		$obj = $response->data[0];
		if($_GET['controller'] == 'mobile')
		{
			$opposite = 'application';

			// ########################
			// TRAITEMENT DE FORMULAIRE
			// ########################
			if(!empty($_POST))
			{
				if(isset($_POST['deleteRelations']) && !empty($_POST['id']))
				{
					$response = Request::delete(API . '/mobile/' . $_POST['id'] . '/applications');
				}
				elseif(isset($_POST['deleteRelation']) && !empty($_POST['id']) && !empty($_POST['ido']))
				{
					$response = Request::delete(API . '/mobile/' . $_POST['id'] . '/application/' . $_POST['ido']);
				}
				elseif(isset($_POST['addRelation']) && !empty($_POST['id']))
				{
					$_POST['ida'] = $_POST['ido'];
					unset($_POST['addRelation'], $_POST['ido']);
					$response = Request::post(API . '/mobile/' . $_POST['id'] . '/applications', http_build_query($_POST));
				}
				else{
					$response = (object) array(
						'success' => false,
						'data' => (object) array('message' => 'Les paramètres de la requête sont invalides')
					);
				}

				if($response->success)
				{
					$responseText .= 'Requête traitée avec succès<br>';
				}
				else
				{
					$responseText .= '<b>Erreur lors de la requête : ' . $response->data->message . '</b><br>';
				}
			}

			$relations = Request::get(API . '/mobile/' . $_GET['id'] . '/applications');
			if($relations->success)
				$relations = $relations->data;
			else
				$responseText .= '<b>Erreur lors de la récupération des mobiles : ' . $relations->data->message . '</b><br>';
		}
		elseif($_GET['controller'] == 'application')
		{
			$opposite = 'mobile';

			// ########################
			// TRAITEMENT DE FORMULAIRE
			// ########################
			if(!empty($_POST))
			{
				if(isset($_POST['deleteRelations']) && !empty($_POST['id']))
				{
					$response = Request::delete(API . '/application/' . $_POST['id'] . '/mobiles');
				}
				elseif(isset($_POST['deleteRelation']) && !empty($_POST['id']) && !empty($_POST['ido']))
				{
					$response = Request::delete(API . '/application/' . $_POST['id'] . '/mobile/' . $_POST['ido']);
				}
				elseif(isset($_POST['addRelation']) && !empty($_POST['id']))
				{
					$_POST['idm'] = $_POST['ido'];
					unset($_POST['addRelation'], $_POST['ido']);
					$response = Request::post(API . '/application/' . $_POST['id'] . '/mobiles', http_build_query($_POST));
				}
				else{
					$response = (object) array(
						'success' => false,
						'data' => (object) array('message' => 'Les paramètres de la requête sont invalides')
					);
				}

				if($response->success)
				{
					$responseText .= 'Requête traitée avec succès<br>';
				}
				else
				{
					$responseText .= '<b>Erreur lors de la requête : ' . $response->data->message . '</b><br>';
				}
			}

			$relations = Request::get(API . '/application/' . $_GET['id'] . '/mobiles');
			if($relations->success)
				$relations = $relations->data;
			else
				$responseText .= '<b>Erreur lors de la récupération des mobiles : ' . $relations->data->message . '</b><br>';
		}
		else
			$responseText .= '<b>Erreur lors de la requête, mauvais contrôleur</b><br>';
	}
	else
		$responseText .= '<b>Erreur lors de la requête : ' . $response->data->message . '</b><br>';
}
else
	$responseText .= '<b>Erreur lors de la requête, il manque des paramètres</b><br>';


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

	<?php if($obj): ?>

		<h1><?= $_GET['controller'] . ' ' . $_GET['id']; ?></h1>
		<p>
			<?php
				foreach ($obj as $key => $value) {
					echo $key . ' : ' . $value . ' | ';
				}
			?>
			<br>
			<a href="index.php">Retourner à l'index</a>
		</p>

		<h2>Liste des <?= $opposite; ?>s</h2>
		<form action="#" method="post">
			<input type="hidden" name="id" value="<?= $obj->id; ?>">
			<input type="submit" name="deleteRelations" value="tout supprimer">
		</form>

		<table border>
			<tr>
				<?php if($opposite == 'mobile'): ?>
					<th>Id</th>
					<th>Owner</th>
					<th>Version</th>
				<?php elseif($opposite == 'application'): ?>
					<th>Id</th>
					<th>Name</th>
					<th>Version</th>
					<th>Type</th>
				<?php endif; ?>
				<th>Action</th>
			</tr>
			<?php foreach ($relations as $relation): ?>
				<tr>
					<form action="#" method="post">
						<input type="hidden" name="id" value="<?= $obj->id; ?>">
						<input type="hidden" name="ido" value="<?= $relation->id; ?>">
						<?php foreach ($relation as $field): ?>
							<td><?= $field; ?></td>
						<?php endforeach; ?>
						<td>
							<input type="submit" name="deleteRelation" value="supprimer">
						</td>
					</form>
				</tr>
			<?php endforeach; ?>
			<tr>
				<form action="#" method="post">
					<input type="hidden" name="id" value="<?= $obj->id; ?>">
					<td><input type="text" name="ido" required></td>
					<?php if($opposite == 'mobile'): ?>
						<td></td>
						<td></td>
					<?php elseif($opposite == 'application'): ?>
						<td></td>
						<td></td>
						<td></td>
					<?php endif; ?>
					<td><input type="submit" name="addRelation" value="ajouter"></td>
				</form>
			</tr>
		</table>
	<?php endif; ?>

</body>
</html>