<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Resolution
 *
 * @author cfg
 */
class Resolution {
	public static function resolveUrl($urlstr, NetspaceKvs &$nio) {
		
		$url = new \SpringDvs\Url($urlstr);
		if(!empty($url->gtn())) {
			if(!emtpy($url->glq())) {
				// Handle Geloc
			} else {
				// Don't need no stinkin' GTN
				array_pop($url->route());
			}
		}
		
		// Check to see if we are a root on the specified geosub
		if(count($url->route()) > 1) {
			if(end($url->route()) == \SpringDvs\Config::$net['geosub']) {
				array_pop($url->route());
			}
		}
		
		if(count($url->route()) == 1) {
			$nodesn = end($url->route());
			$node = $nio->gsnNodeBySpringName($nodesn);
			$frame = new SpringDvs\FrameNodeInfo(
					200, 
					$node->types(),
					$node->service(),
					$node->address(),
					$node->toNodeRegister());
			
			return SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response_node_info,
					$frame->serialise()
				);
			
		} else if(count($url) > 1) {
			$rootNodes = $nio->gtnGeosubRootNodes(end($url->route()));
			
			if(empty($rootNodes)) return false;

			$frame = new \SpringDvs\FrameResolution($url->toString());
			$packet = SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_resolution,
					$frame->serialise()
				);
			
			// Chain the request to the next node
			// We are only using the first one for now
			$response = \SpringDvs\HttpService::sendPacket(
					$packet, 
					\SpringDvs\Node::addressToString($rootNodes[0]->address())
				);
			
			return $reponse;
		} else {
			return false;
		}
	}
}
