<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

include 'User.php';

Flight::register('manApi', 'ManagementApiController');
Flight::register('springApi', 'SpringApiController');
Flight::register('gatewayApi', 'GatewayApiController');

Flight::route('/node/(@area/(@action/(@method(/@service))))', function($area, $zone, $method, $service, $route) {
	
	session_start();
	
	$updater = new UpdateCheck(new VersionHandler(), new ModuleHandler(), new CoreHandler(), new SystemUpdateKvs());
		
	$scriptsTop =  array();
	$scriptsBottom =  array();
	
	$masterView = 'master_node_config';
	if( ($token = filter_input(INPUT_POST, '__token')) !== null) {
		if($token == \SpringDvs\Config::$sys['api_token'] && $area == 'api') {
			define('NODE_ADMIN', true); // We are in admin mode
			$updater->check(CHK_UPDATE_MODULES|CHK_UPDATE_CORE);
			
			$api = Flight::manApi();
			$json = $api->request($route->params);
			Flight::render('master_json', array('json' => $json));
			return;
		}

		Flight::render('master_json', array('json' => 'unauthorised'));
		return;	
	}
	
	if(!isset($_SESSION['admin'])) {
		$v = check_login();
		if($v < 2) {
			Flight::render('node_login', ['passState' => $v], 'body_content');
			Flight::render('master_general', array(
										'scriptsTop' => [],
										'scriptsBottom' => [],
				));
			return;
		}
	}
	
	
	
	define('NODE_ADMIN', true); // We are in admin mode
	define('NODE_LOCAL', true); // We are working on the local system

	$updater->check(CHK_UPDATE_MODULES|CHK_UPDATE_CORE);

	switch($area) {
	case 'api':
		$api = Flight::manApi();
		$json = $api->request($route->params);
		Flight::render('master_json', array('json' => $json));
		return;

	case 'service':
		$file = __DIR__."/modules/".$zone."/".$method."/config/view.php";
		
		if(!file_exists($file)) {
			Flight::render('error', null, 'body_content');
			break;
		}
		Flight::render($file, null, 'body_content');
		
		$script = "/modules/".$zone."/".$method."/config/client.js";
		if(file_exists(__DIR__.$script)) {
			$scriptsTop[] = "../..".$script;
		}
		$masterView = 'master_service_config';
		break;
		
	case 'logout':
		unset($_SESSION['admin']);
		Flight::redirect('/node/');
		break;
	default:
		$scriptsTop[] = "api_man_overview.js";
		Flight::render('node_config_overview', null, 'body_content');
	}
	
	Flight::render($masterView, array(
										'scriptsTop' => $scriptsTop,
										'scriptsBottom' => $scriptsBottom,
				));
	
}, true);

Flight::route('/spring/', function() {
	// This is the server interface for the node
	$response = Flight::springApi()->request();
	Flight::render('master_spring', array('response' => $response));
	
}, true);

Flight::route('/gateway/@service/*', function($service, $route) {
	
	// This is the gateway interface for the node
	
	$response = Flight::gatewayApi()->request($service);
	Flight::render('master_json', array('json' => $response));
	
}, true);


