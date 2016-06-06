<?php
include __DIR__.'/../handlers/GatewayHandler.php';

/**
 * Provide the controller interface for gateway services
 * 
 * This is an internal object that passes the request onto the 
 * gateway request handler
 *
 */
class GatewayApiController {
	public function request($service) {
		
		$out = GatewayHandler::request($service);
		$callback = filter_input(INPUT_GET, 'callback');
		if($callback) {
			$out = "$callback($out);";
		}
		return $out;
	}
}