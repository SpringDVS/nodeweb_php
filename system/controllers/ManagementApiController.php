<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class ManagementApiController {
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
				return array();
				
				break;
		}

		if(!method_exists($this, $method)) return array();
		
		return $this->$method();
	}
	
	private function overviewGet() {
		return json_encode( array(
			'springname' => \SpringDvs\Config::$spec['springname'],
			'hostname' => \SpringDvs\Config::$spec['hostname'],
			'service' => \SpringDvs\Config::$net['service'],
			'address' => $_SERVER['SERVER_ADDR'],
			
			'master_addr' => \SpringDvs\Config::$net['master'],
			'geosub' => \SpringDvs\Config::$net['geosub'] . '.' . \SpringDvs\Config::$net['geotop'],
			'status' => "Unknown",
			'register' => "Unregistered",
		) );
	}
	
	private function registerPost() {
		$frame = new SpringDvs\FrameRegistration(
						true, 
						SpringDvs\DvspNodeType::org, 
						SpringDvs\DvspService::http, 
						SpringDvs\nodereg_from_config(),
						SpringDvs\Config::$spec['token']);
		
		$p = SpringDvs\DvspPacket::ofType(SpringDvs\DvspMsgType::gsn_registration, $frame->serialise());
		$packet = SpringDvs\HttpService::sendPacket($p, SpringDvs\Config::$net['master'], SpringDvs\hostres_from_config());
		return \SpringDvs\HttpService::jsonEncodePacket($packet);
	}
	
	private function statePost() {
		if(!isset($_GET['state'])) return "{}";
		
		$state = $_GET['state'];
		if($state == "enabled") {
			$frame = new SpringDvs\FrameStateUpdate(SpringDvs\DvspNodeState::enabled, \SpringDvs\Config::$spec['springname']);
		} else {
			$frame = new SpringDvs\FrameStateUpdate(SpringDvs\DvspNodeState::disabled, \SpringDvs\Config::$spec['springname']);
		}
		$p = SpringDvs\DvspPacket::ofType(SpringDvs\DvspMsgType::gsn_state, $frame->serialise());
		$packet = SpringDvs\HttpService::sendPacket($p, SpringDvs\Config::$net['master'], SpringDvs\hostres_from_config());	
		return \SpringDvs\HttpService::jsonEncodePacket($packet);
	}
	
	private function stateGet() {
		$springname = \SpringDvs\Config::$spec['springname'];
		
		if(isset($_GET['springname'])) $springname = $_GET['springname'];
		
		$frame = new SpringDvs\FrameStatusRequest($springname);

		$p = SpringDvs\DvspPacket::ofType(SpringDvs\DvspMsgType::gsn_node_status, $frame->serialise());
		$packet = SpringDvs\HttpService::sendPacket($p, SpringDvs\Config::$net['master'], SpringDvs\hostres_from_config());

		return \SpringDvs\HttpService::jsonEncodePacket($packet);
	}
	
	private function gwservicesGet() {
		return json_encode($this->extractServices('system/modules/gateway/'));
	}
	
	private function nwservicesGet() {
		return json_encode($this->extractServices('system/modules/network/'));
	}
	
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
}