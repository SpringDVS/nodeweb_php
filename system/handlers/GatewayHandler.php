<?php

class GatewayHandler {

	public static function request($request) {
		return json_encode(array('result' => 'success'));
	}
}
