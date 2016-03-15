<?php
include 'User.php';
include 'system/controllers/ManagementApiController.php';

Flight::register('user', 'User');
Flight::register('manApi', 'ManagementApiController');

Flight::route('/node/(@area/(@zone/(@method)))', function($area, $zone, $method, $route) {
	$user = Flight::user();
	$scriptsTop =  array();
	$scriptsBottom =  array();
	
	// Check login here!!

	switch($area) {
	case 'api':
		$api = Flight::manApi();
		$json = $api->request($route->params);
		Flight::render('master_json', array('json' => $json));
		return;

	case 'network':
		Flight::render('node_config_netman', null, 'body_content');
		break;

	default:
		$scriptsBottom[] = "api_man_overview.js";
		Flight::render('node_config_overview', null, 'body_content');
	}
	
	Flight::render('master_node_config', array(
										'scriptsTop' => $scriptsTop,
										'scriptsBottom' => $scriptsBottom,
				));
	
}, true);


