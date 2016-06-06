<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Handler for gateway requests.
 * 
 * As nodes act as gateways to the Spring network, this is used to
 * handle certain requests made of the node, and perform that request
 * internally on the network, providing the response once received.
 * 
 * Each node provides particular interfaces to the network through
 * gateway services, and these are what the external system works
 * through. Gateway services will likely have a complimentary 
 * network service for which they are providing the interface.
 * 
 */
class GatewayHandler {

	/**
	 * Perform the gateway service request
	 * 
	 * @param string $service Which gateway service is to use
	 * @return string an encoded response string (likely to be JSON)
	 */
	public static function request($service) {
		$path = "system/modules/gateway/$service/request.php";
		
		if(!file_exists($path)) {
			return json_encode(array('service' => 'error'));
		}
		
		$response = include "$path";
		
		return json_encode(
				array_merge(['service' => $service],$response)
			);
	}
}
