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
	
	/**
	 * Perform a resolution of a Spring URI.
	 * 
	 * If there is an error then it returns false otherwise
	 * it will return an array of objects that implement the
	 * INodeNetInterface
	 * 
	 * @param string $uri
	 * @return array(\SpringDvs\INodeNetInterface) | false 
	 */
	public static function resolveUri($uri) {
		$message = SpringDvs\Message::fromStr("resolve $uri");
		
		$response = SpringDvs\HttpService::send($message, \SpringDvs\Config::$net['primary'],  \SpringDvs\Config::$net['hostname']);
		
		if(!$response
		|| $response->cmd() != \SpringDvs\CmdType::Response
		|| $response->content()->code() != \SpringDvs\ProtocolResponse::Ok) {
			return false;
		}
		$type = $response->content()->type();
		switch($type) {
			case \SpringDvs\ContentResponse::Network:
				return $response->content()->content()->nodes();
			case \SpringDvs\ContentResponse::NodeInfo:
				return array($response->content()->content());
			default:
				return false;
		}
		
	}
	
	/**
	 * Perform a request and accept first response
	 * 
	 *  This method takes an array of potential target nodes
	 *  and if the request fails, it moves onto the next one.
	 *  
	 *  If there is no valid response then the entire method
	 *  fails by returning null.
	 * 
	 * @param \SpringDvs\Message $msg
	 * @param array $nodes
	 * @return mixed \SpringDvs\Message on success | null on failure
	 */
	public static function outboundFirstResponse(\SpringDvs\Message $msg, array $nodes) {
		
		foreach($nodes as $node) {
			$response = SpringDvs\HttpService::send($msg, $node->address(),  $node->host());

			if($response === false
			|| $response->cmd() != \SpringDvs\CmdType::Response
			|| $response->content()->code() != \SpringDvs\ProtocolResponse::Ok) {
				continue;
			}
			return $response;
		}
		
		return null;
	}
}
