<?php
include __DIR__.'/../handlers/GatewayHandler.php';
class GatewayApiController {
	public function request($service) {
		
		$out = GatewayHandler::request($service);
		return $out;
	}
}