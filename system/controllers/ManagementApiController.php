<?php

class ManagementApiController {
	public function request($func) {
		
		$method = "";
		switch ($func['method']) {
			case null:
			case 'get':
			case 'pull':
				$method = $func['zone'] . "Get";
				break;

			case 'post':
			case 'push':
				$method = $func['zone'] . "Post";
				break;
			default:
				return array();
				
				break;
		}

		if(!method_exists($this, $method)) return array();
		
		return $this->$method();
	}
	
	private function overviewGet() {
		return array(
			'springname' => \SpringDvs\Config::$spec['springname'],
			'hostname' => \SpringDvs\Config::$spec['hostname'],
			'service' => \SpringDvs\Config::$net['service'],
			'address' => $_SERVER['SERVER_ADDR'],
			
			'master_addr' => \SpringDvs\Config::$net['master'],
			'geosub' => 'esusx.uk',
		);
	}
	
	private function registerPost() {
		$frame = new SpringDvs\FrameRegistration(
						true, 
						SpringDvs\DvspNodeType::org, 
						SpringDvs\DvspService::http, 
						SpringDvs\nodereg_from_config());
		
		$p = SpringDvs\DvspPacket::ofType(SpringDvs\DvspMsgType::gsn_registration, $frame->serialise());
		$packet = SpringDvs\HttpService::sendPacket($p, SpringDvs\Config::$net['hostname']);
		return \SpringDvs\HttpService::jsonEncodePacket($packet);
	}
}