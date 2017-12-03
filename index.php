<?php

$site = $_GET['site'] ?? $_SERVER['SERVER_NAME'];
$site .= '/API';

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Informations</title>
</head>
<body>
	<h1>Informations</h1>

	<h3>V2 (principaux changements)</h3>
	<ul>
		<li>Mise à jour des informations de cette page</li>
		<li>Séparation du projet en 3 parties (interface web (/web), l'API (/API), les outils (/PraddApp)</li>
		<li>Ajout d'un autoloader, d'un système d'exceptions, d'une classe Response</li>
		<li>BDD : ajout d'un index unique application(name,version), changement total des indexes et contraintes de application_mobile, rien pour
			mobile (je ne trouve pas que ce soit très pertinent vu que le contexte)</li>
		<li>Prise en charge des codes HTTP dans la réponse (200, 201, 204, 404, 405, 500), ainsi que du content-type (json)</li>
		<li>Vérificaton renforcée des données fournies par l'utilisateur (id valide)</li>
		<li>Indication de la provenance d'une erreur</li>
	</ul>

	<h3>Divers</h3>
	<p>
		PHP 7 minimum est requis, certains tests et déclarations de méthodes en utilisent la syntaxe.<br>
		Vous pouvez modifier l'url des commandes curl en rajoutant un paramètre site dans l'url (?site=localhost/proj).<br>
		Pour la partie routing je me suis inspiré d'un tutoriel de Grafikart que j'ai adapté à mes besoins (utilisation d'objet notamment).
	</p>

	<h3>Description</h3>
	<ol>
		<li>Contrôleur
			<ol>
				<li>MÉTHODE
					<ul>
						<li>schéma de l'url => exemple de commande CURL (rajouter -I pour voir le header response) => informations supplémentaires</li>
					</ul>
				</li>
			</ol>
		</li>
	</ol>

	<h3>Méthodes</h3>
	<ul>
		<li>GET => retourne une information en JSON => SELECT</li>
		<li>POST => retourne un booléen indiquant le succès (true) ou l'échec (false) de l'insertion => INSERT</li>
		<li>PUT => retourne un booléen indiquant le succès (true) ou l'échec (false) de la mise à jour => UPDATE</li>
		<li>DELETE => retourne un booléen indiquant le succès (true) ou l'échec (false) de la suppression => DELETE</li>
	</ul>

	<a href="/web"><h3>Interface Web</h3></a>

	<h3>API</h3>
	<ol>
		<li>Mobile
			<ol>
				<li>GET
					<ul>
						<li>/mobiles => curl -X GET http://<?= $site; ?>/mobiles => retourne tous les mobiles</li>
						<li>/mobile/:id => curl -X GET http://<?= $site; ?>/mobile/1 => retourne le mobile d'id 1</li>
						<li>/mobile/:id/applications => curl -X GET http://<?= $site; ?>/mobile/1/applications => retourne toutes les applications du mobile d'id 1</li>
						<li>/mobile/:idm/application/:ida => curl -X GET http://<?= $site; ?>/mobile/1/application/2 => retourne l'application d'id 2 du mobile d'id 1 ou false</li>
					</ul>
				</li>
				<li>POST
					<ul>
						<li>/mobiles => curl -X POST http://<?= $site; ?>/mobiles -d "owner=John&version=7.0" => ajoute un mobile</li>
						<li>/mobile/:id/applications => curl -X POST http://<?= $site; ?>/mobile/1/applications -d "ida=2" => ajoute l'application d'id 2 au mobile 1</li>
					</ul>
				</li>
				<li>PUT
					<ul>
						<li>/mobile/:id => curl -X PUT http://<?= $site; ?>/mobile/1 -d "owner=Johnn&version=7.1" => met à jour le mobile d'id 1</li>
					</ul>
				</li>
				<li>DELETE
					<ul>
						<li>/mobile/:id => curl -X DELETE http://<?= $site; ?>/mobile/1 => supprime le mobile d'id 1</li>
						<li>/mobile/:id/applications => curl -X DELETE http://<?= $site; ?>/mobile/1/applications => supprime les applications du mobile d'id 1</li>
						<li>/mobile/:idm/application/:ida => curl -X DELETE http://<?= $site; ?>/mobile/1/application/2 => supprime l'application d'id 2 du mobile d'id 1</li>
					</ul>
				</li>
			</ol>
		</li>
		<li>Application
			<ol>
				<li>GET
					<ul>
						<li>/applications => curl -X GET http://<?= $site; ?>/applications => retourne toutes les applications</li>
						<li>/application/:id => curl -X GET http://<?= $site; ?>/application/1 => retourne l'application d'id 1</li>
						<li>/application/:id/mobiles => curl -X GET http://<?= $site; ?>/application/1/mobiles => retourne tous les mobiles possèdant l'application d'id 1</li>
						<li>/application/:ida/mobile/:idm => curl -X GET http://<?= $site; ?>/application/1/mobile/2 => retourne le mobile d'id 2 possèdant l'application d'id 1 ou false</li>
					</ul>
				</li>
				<li>POST
					<ul>
						<li>/applications => curl -X POST http://<?= $site; ?>/applications -d "name=App&version=1.0&type=jeu" => ajoute une application</li>
						<li>/application/:id/mobiles => curl -X POST http://<?= $site; ?>/application/1/mobiles -d "idm=2" => ajoute le mobile d'id 2 comme possesseur de l'application d'id 1</li>
					</ul>
				</li>
				<li>PUT
					<ul>
						<li>/application/:id => curl -X PUT http://<?= $site; ?>/application/1 -d "name=Apy&version=1.0&type=autre" => met à jour l'application d'id 1</li>
					</ul>
				</li>
				<li>DELETE
					<ul>
						<li>/application/:id => curl -X DELETE http://<?= $site; ?>/application/1 => supprime l'application d'id 1</li>
						<li>/application/:id/mobiles => curl -X DELETE http://<?= $site; ?>/application/1/mobiles => supprime l'application d'id 1 des mobiles</li>
						<li>/application/:ida/mobile/:idm => curl -X DELETE http://<?= $site; ?>/application/1/mobile/2 => supprime l'application d'id 1 du mobile d'id 2</li>
					</ul>
				</li>
			</ol>
		</li>
	</ol>
</body>
</html>