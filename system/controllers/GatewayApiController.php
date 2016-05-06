<?php
include 'handlers/GatewayHandler.php';
class GatewayApiController {
	public function request() {
		$request = Flight::request()->getBody();
		$out = GatewayHandler::request($request);
		return $out;
	}
}