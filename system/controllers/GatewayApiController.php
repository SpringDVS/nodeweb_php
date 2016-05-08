<?php
include __DIR__.'/../handlers/GatewayHandler.php';
class GatewayApiController {
	public function request($service) {
		
		$out = GatewayHandler::request($service);
		$callback = filter_input(INPUT_GET, '__cb');
		if($callback) {
			$out = "$callback($out);";
		}
		return $out;
	}
}