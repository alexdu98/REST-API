<?php

require '../PradApp/Autoloader/Autoloader.php';
PradApp\Autoloader\Autoloader::register('../');

use PradApp\Routing\Router;


// PHP 7
$url = $_GET['url'] ?? '/';
// PHP 5
// $url = isset($_GET['url']) ? $_GET['url'] : '/';
$router = new Router($url);


// Routes Mobile
$router->get('/mobiles', 'PradApp\Controller\Mobile', 'getMobiles');
$router->get('/mobile/:id', 'PradApp\Controller\Mobile', 'getMobileById');
$router->get('/mobile/:id/applications', 'PradApp\Controller\Mobile', 'getApplicationsOfMobileId');
$router->get('/mobile/:idm/application/:ida', 'PradApp\Controller\Mobile', 'getApplicationIdOfMobileId');

$router->post('/mobiles', 'PradApp\Controller\Mobile', 'postMobiles');
$router->post('/mobile/:id/applications', 'PradApp\Controller\Mobile', 'postApplicationsOfMobileId');

$router->put('/mobile/:id', 'PradApp\Controller\Mobile', 'putMobileById');

$router->delete('/mobile/:id', 'PradApp\Controller\Mobile', 'deleteMobileById');
$router->delete('/mobile/:id/applications', 'PradApp\Controller\Mobile', 'deleteApplicationsOfMobileId');
$router->delete('/mobile/:idm/application/:ida', 'PradApp\Controller\Mobile', 'deleteApplicationIdOfMobileId');


// Routes Application
$router->get('/applications', 'PradApp\Controller\Application', 'getApplications');
$router->get('/application/:id', 'PradApp\Controller\Application', 'getApplicationById');
$router->get('/application/:id/mobiles', 'PradApp\Controller\Application', 'getMobilesOfApplicationId');
$router->get('/application/:ida/mobile/:idm', 'PradApp\Controller\Application', 'getMobileIdOfApplicationId');

$router->post('/applications', 'PradApp\Controller\Application', 'postApplications');
$router->post('/application/:id/mobiles', 'PradApp\Controller\Application', 'postMobilesOfApplicationId');

$router->put('/application/:id', 'PradApp\Controller\Application', 'putApplicationById');

$router->delete('/application/:id', 'PradApp\Controller\Application', 'deleteApplicationById');
$router->delete('/application/:id/mobiles', 'PradApp\Controller\Application', 'deleteMobilesOfApplicationId');
$router->delete('/application/:ida/mobile/:idm', 'PradApp\Controller\Application', 'deleteMobileIdOfApplicationId');

try{
	// Routage
	$response = $router->run();
	$response->send();
}
catch(\PradApp\Exception\Exception $e)
{
	$e->send();
}