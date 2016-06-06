<?php
use SpringDvs;
use SpringDvs\DvspPacket;

/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Provides a means of resolving Spring URI to an IP address
 * 
 * This implements the Name Resolution section of the DVSP networking specification 
 */
class Resolution {
	
	/**
	 * Resolve the provided URL into an IP Address
	 * @param string $urlstr The URI to resolve
	 * @param NetspaceKvs $nio The netspace database to check against
	 * @return \SpringDvs\DvspPacket response; otherwise false
	 */
	public static function resolveUrl($urlstr, NetspaceKvs &$nio) {
		
		$url = new \SpringDvs\Url($urlstr);
		$gtn = $url->gtn();
		if(!empty($gtn)) {
			$glq = $url->glq();
			if(!empty($glq)) {
				// Handle Geloc
			} else {
				// Don't need no stinkin' GTN
				$url->pop();
			}
		}
		
		// Check to see if we are a root on the specified geosub
		if(count($url->route()) > 1) {
			if(end($url->route()) == \SpringDvs\Config::$net['geosub']) {
				$url->pop();
			}
		}
		
		if(count($url->route()) == 1) {
			$nodesn = end($url->route());
			$node = $nio->gsnNodeBySpringName($nodesn);
		
			if(!$node) return false;

			$frame = new SpringDvs\FrameNodeInfo(
					200, 
					$node->type(),
					$node->service(),
					$node->address(),
					$node->toNodeRegister());
			
			return SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response_node_info,
					$frame->serialise()
				);
			
		} else if(count($url->route()) > 1) {
			
			$rootNodes = $nio->gtnGeosubRootNodes(end($url->route()));

			if(empty($rootNodes)) {
					$frame = new SpringDvs\FrameResponse(SpringDvs\DvspRcode::netspace_error);
					return SpringDvs\DvspPacket::ofType(
						\SpringDvs\DvspMsgType::gsn_response, 
						$frame->serialise()
					);
			}
			$url->pop();
			$frame = new \SpringDvs\FrameResolution($url->toString());
			$packet = SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_resolution,
					$frame->serialise()
				);
			
			// Chain the request to the next node
			// We are only using the first one for now
			$response = \SpringDvs\HttpService::sendPacket(
					$packet, 
					\SpringDvs\Node::addressToString($rootNodes[0]->address()),
					$rootNodes[0]->toHostResource()
				);
			
			return $response;
		} else {
			return false;
		}
	}
}
