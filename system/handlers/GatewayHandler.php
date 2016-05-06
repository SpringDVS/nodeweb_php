<?php

class GatewayHandler {

	public static function request($service) {
		$path = "system/modules/gateway/$service/request.php";
		
		if(!file_exists($path)) {
			return json_encode(array('response' => 'error'));
		}
		
		$response = include "$path";
		return json_encode(array(
			'response' => 'ok',
			'content' => $response
			));
	}
}
