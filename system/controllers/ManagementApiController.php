<?php
use SpringDvs\Message;

/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Provide all the functionality for node management via web API
 *
 */
class ManagementApiController {

	/**
	 * Resolve and run an action
	 * 
	 * Resovles the method by convention. example:
	 * 
	 *   /api/overview/get/ => ManagementApiContoller::overviewGet()
	 *   /api/updates/post/ => ManagementApiContoller::updatesPost()
	 *   
	 * @param array $func Flight route array
	 * @return JSON encoded string response
	 */
	public function request($func) {

		$method = "";
		switch ($func['method']) {
			case null:
			case 'get':
			case 'pull':
				$method = $func['action'] . "Get";
				break;

			case 'post':
			case 'push':
				$method = $func['action'] . "Post";
				break;
			default:
				return "{}";
				
				break;
		}
		
		if(!method_exists($this, $method)) return json_encode(array());
		$ref = new ReflectionMethod('ManagementApiController', $method);
		
		if($ref->getNumberOfParameters() == 1) {
			return $this->$method($func['service']);
		}
		return $this->$method();
	}
	
	/**
	 * Retrieve the overview details of the node
	 * 
	 * 
	 * This cannot be used through remote API
	 */
	private function overviewGet() {
		if(!defined('NODE_LOCAL')) return "{}";
		
		return json_encode( array(
			'springname' => \SpringDvs\Config::$spec['springname'],
			'hostname' => \SpringDvs\Config::$spec['hostname'],
			'service' => \SpringDvs\Config::$net['service'],
			'address' => $_SERVER['SERVER_ADDR'],
			
			'primary_addr' => \SpringDvs\Config::$net['primary'],
			'geosub' => \SpringDvs\Config::$net['geosub'] . '.' . \SpringDvs\Config::$net['geotop'],
			'status' => "Unknown",
			'register' => "Unregistered",
			'nwservices' => [],
			'gwservices' => [],
			'updates' => [],
		) );
	}
	
	/**
	 * Get the network uri of node
	 */
	private function springnetGet() {
		return json_encode([
			'springname' => \SpringDvs\Config::$spec['springname'],
			'network' => \SpringDvs\Config::$net['geosub'] . '.' . \SpringDvs\Config::$net['geotop']
				]);
	}
	
	/**
	 * Get the list of packages queued for updates
	 */
	private function updatesGet() {
		$checker = new UpdateCheck(new VersionHandler(), new ModuleHandler(), new CoreHandler(), new SystemUpdateKvs());
		$queue = $checker->getUpdateQueue();
		$out = array(
				array('mtype' => 'Network', 'modules' => array()),
				array('mtype' => 'Gateway', 'modules' => array()),
				array('mtype' => 'Core', 'modules' => array())
			);
		$index = 0;
		foreach($queue as $prefix => $type) {
			foreach($type as $module => $details) {
				unset($details['sha1']);
				$out[$index]['modules'][] = array('module' => $module, 'details' => $details);
			}
			$index++;
		}
		
		return json_encode($out);
	}
	
	/**
	 * Perform update action across all queued packages
	 * 
	 * Warning: This action is desctructive!
	 */
	private function updatesPost() {
		$status = array('nws' => array(),'gws' => array(),'core' => array());
		$checker = new UpdateCheck(new VersionHandler(), new ModuleHandler(), new CoreHandler(), new SystemUpdateKvs());
		$runner = new UpdateRunner(new PackageHandler());
		$queue = $checker->getUpdateQueue();

		foreach($queue['core'] as $module => $info) {
			$status['core'][$module] = $runner->core($info);
		}

		foreach($queue['nws'] as $module => $info) {
			$status['nws'][$module] = $runner->serviceNetwork($module, $info);
		}

		foreach($queue['gws'] as $module => $info) {
			$status['gws'][$module] = $runner->serviceGateway($module, $info);
		}

		$checker->check(CHK_UPDATE_MODULES|CHK_UPDATE_CORE, true);
		return json_encode($status);
	}
	
	/**
	 * Perform a node registration with the GSN
	 * 
	 * This cannot be used through remote API
	 */
	private function registerPost() {
		if(!defined('NODE_LOCAL')) return "{}";

		$nodeDouble = \SpringDvs\nodedouble_from_config();
		$token = SpringDvs\Config::$spec['token'];
		$m = Message::fromStr("register {$nodeDouble};org;http;$token");
		$response = SpringDvs\HttpService::send($m, SpringDvs\Config::$net['primary'], \SpringDvs\Config::$net['hostname']);
		return json_encode($response->toJsonArray());
	}
	
	/**
	 * Update the state of the node in the GSN
	 * 
	 * This cannot be used through remote API
	 */
	private function statePost() {
		if(!defined('NODE_LOCAL')) return '{}';
		
		if(!isset($_GET['state'])) return "{}";
		
		$state = $_GET['state'];
		
		if($state == "enabled") {
			$msg = Message::fromStr('update '.\SpringDvs\Config::$spec['springname'].' state enabled');
		} else {

			$msg = Message::fromStr('update '.\SpringDvs\Config::$spec['springname'].' state disabled');
		}
		
		$response = SpringDvs\HttpService::send($msg, SpringDvs\Config::$net['primary'], SpringDvs\Config::$net['hostname']);	
		return json_encode($response->toJsonArray());
	}
	
	/**
	 * Request the state of the node in the GSN
	 * 
	 * This cannot be used through remote API
	 */
	private function stateGet() {
		if(!defined('NODE_LOCAL')) return "{}";

		$springname = \SpringDvs\Config::$spec['springname'];
		
		if(isset($_GET['springname'])){ $springname = $_GET['springname']; }
		
		$msg = SpringDvs\Message::fromStr("info node $springname state");
		
		$response = SpringDvs\HttpService::send($msg, SpringDvs\Config::$net['primary'], SpringDvs\Config::$net['hostname']);	
		return json_encode($response->toJsonArray());
	}
	
	/**
	 * Get list of gateway services
	 */
	private function gwservicesGet() {
		return json_encode($this->extractServices('system/modules/gateway/'));
	}
	
	/**
	 * get list of network services
	 */
	private function nwservicesGet() {
		return json_encode($this->extractServices('system/modules/network/'));
	}
	
	/**
	 * extract services of type
	 * @todo Use the module handler instead
	 * 
	 * @param string $root The servoce type 
	 */
	private function extractServices($root) {
		$dirs = array_filter(glob($root.'/*'), 'is_dir');
		$result = array();
		foreach($dirs as $d) {
			$path = $d.'/info.php';
			if(!file_exists($path)) continue;
			
			$r = include $path;
			if(!is_array($r)) continue;
			$result[] = $r;
		}
		return $result;		
	}
	
	/**
	 * Pass a get request onto a network service
	 * 
	 * @param string $service The service to request
	 */
	private function nwserviceGet($service) {
		return $this->networkService($service, 'Get');
	}
	
	/**
	 * Pass a post request onto a network service
	 *
	 * @param string $service The service to request
	 */
	private function nwservicePost($service) {
		return $this->networkService($service, 'Post');
	}
	
	/**
	 * Perform the request with the specified network service
	 * 
	 * @param string $service The service to request
	 * @param string $method The method of request (get|post)
	 */
	private function networkService($service, $method) {
		$file = "system/modules/network/$service/config/controller.php";
		
		if( !file_exists($file) ) {
			return json_encode(array("service" => "error"));
		}
		include $file;
		$controller = new ServiceController();
		return $controller->request($method);
	}
	
	private function keyringGet($service) {
		$controller = new KeyringController();
		return $controller->request($service, 'Get');
	}
	
	private function keyringPost($service) {
		$controller = new KeyringController();
		return $controller->request($service, 'Post');
	}
}
