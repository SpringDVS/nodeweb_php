<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * The protocol handler for processing packets as a SpringDVS node
 * and sometimes a root node if it has been elevated
 */
class ProtocolHandler {
	
	/**
	 * Takes the bytes of a request, processes them as a packet,
	 * performs required actions and returns the bytes of a
		 * serialised response packet.
	 * 
	 * @param bytes $bytes
	 * @return bytes of serialised response
	 */
	public static function processBytes($bytes) {
		$nio = new NetspaceKvs(false);
		$packet = SpringDvs\DvspPacket::deserialise($bytes);
		if(!$packet) {
			$response = self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
			return $response->serialise();
		}
		
		switch($packet->header()->type) {
	
		case \SpringDvs\DvspMsgType::gsn_registration:
			return self::processGsnRegistration($packet, $nio)->serialise();

		case \SpringDvs\DvspMsgType::gsn_area:
			return self::processGsnArea($nio)->serialise();
			
		case \SpringDvs\DvspMsgType::gsn_state:
			return self::processFrameStateUpdate($packet, $nio)->serialise();
		
		case \SpringDvs\DvspMsgType::gsn_node_info:
			return self::processFrameNodeRequestInfo($packet, $nio)->serialise();

		case \SpringDvs\DvspMsgType::gsn_node_status:
			return self::processFrameNodeRequestStatus($packet, $nio)->serialise();

		case \SpringDvs\DvspMsgType::gsn_type_request:
			return self::processFrameTypeRequest($packet, $nio)->serialise();
			
		case \SpringDvs\DvspMsgType::gtn_registration:
			return self::processFrameGtnRegister($packet, $nio)->serialise();
			
		case \SpringDvs\DvspMsgType::gtn_geosub_nodes:
			return self::processFrameGeosub($packet, $nio)->serialise();
			
		case \SpringDvs\DvspMsgType::gsn_resolution:
			return self::processFrameResolution($packet, $nio)->serialise();

		case \SpringDvs\DvspMsgType::gsn_request:
			return self::processRequest($packet, $nio)->serialise();
			
		case \SpringDvs\DvspMsgType::unit_test:
			return self::processFrameUnitTest($packet, $nio)->serialise();

		default:
			return self::rcodePacket(\SpringDvs\DvspRcode::malformed_content)->serialise();
		}
	}
	
	/**
	 * Helper function to generate a (regularly used) FrameResponse
	 * packet
	 * 
	 * @param \SpringDvs\DvspRcode $rcode The response code
	 * @return \SpringDvs\DvspPacket
	 */
	private static function rcodePacket($rcode) {
		$frame = new SpringDvs\FrameResponse($rcode);
		return SpringDvs\DvspPacket::ofType(
				\SpringDvs\DvspMsgType::gsn_response, 
				$frame->serialise()
			);
	}
	
	private static function processGsnRegistration(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$frame = $packet->contentAs(SpringDvs\FrameRegistration::contentType());
		
		if(!$frame) self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
			
		$node = SpringDvs\Node::fromNoderegAddr($frame->nodereg, SpringDvs\Node::addressFromString($_SERVER['REMOTE_ADDR']));
		$node->updateService($frame->service);
		$node->updateTypes($frame->type);
		if($frame->register) {
			if(!$nio->gsnNodeRegister($node))
				return self::rcodePacket(\SpringDvs\DvspRcode::netspace_duplication);
			return self::rcodePacket(\SpringDvs\DvspRcode::ok);
		} else {

			$check = $nio->gsnNodeBySpringName($node->springname());
			if(!$check) return self::rcodePacket(\SpringDvs\DvspRcode::netspace_error);

			if($check->addressToString($check->address()) != $_SERVER['REMOTE_ADDR'])
				return self::rcodePacket(\SpringDvs\DvspRcode::network_error);

			if(!$nio->gsnNodeUnregister($node))
				return self::rcodePacket(\SpringDvs\DvspRcode::netspace_error);
			return self::rcodePacket(\SpringDvs\DvspRcode::ok);
		}
	}
	
	private static function processGsnArea(NetspaceKvs &$nio) {
		$nodelist = self::nodelistFromNodes(
				$nio->gsnNodes()
		);
		$frame = new \SpringDvs\FrameNetwork($nodelist);
		return self::forgePacket(SpringDvs\DvspMsgType::gsn_response_network, $frame);
	}
	
	private static function processFrameStateUpdate(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$frame = $packet->contentAs(SpringDvs\FrameStateUpdate::contentType());
		if(!$frame) self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		
		$node = \SpringDvs\Node::from_springname($frame->springname);
		$check = $nio->gsnNodeBySpringName($frame->springname);
		
		if(!$check)
			return self::rcodePacket(\SpringDvs\DvspRcode::netspace_error);

		if($check->addressToString($check->address()) != $_SERVER['REMOTE_ADDR'])
			return self::rcodePacket(\SpringDvs\DvspRcode::network_error);

		$node->updateState($frame->state);
		
		$nio->gsnNodeUpdate($node);
		return self::rcodePacket(\SpringDvs\DvspRcode::ok);
	}
	
	private static function processFrameNodeRequestInfo(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$frame = $packet->contentAs(SpringDvs\FrameNodeRequest::contentType());
		if(!$frame) self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		
		$node = $nio->gsnNodeBySpringName($frame->node);
		if(!$node)
			return self::rcodePacket(\SpringDvs\DvspRcode::netspace_error);
		
		$info = new \SpringDvs\FrameNodeInfo(200, $node->types(), $node->service(), $node->address(), $node->toNodeRegister());
		return self::forgePacket(SpringDvs\DvspMsgType::gsn_response_node_info, $info);
	}
	
	private static function processFrameNodeRequestStatus(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$frame = $packet->contentAs(SpringDvs\FrameNodeRequest::contentType());
		if(!$frame) self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		
		$node = $nio->gsnNodeBySpringName($frame->node);
		if(!$node)
			return self::rcodePacket(\SpringDvs\DvspRcode::netspace_error);
		
		$status = new \SpringDvs\FrameNodeStatus($node->state());
		return self::forgePacket(SpringDvs\DvspMsgType::gsn_response_status, $status);
	}
	
	private static function processFrameTypeRequest(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$frame = $packet->contentAs(SpringDvs\FrameTypeRequest::contentType());
		if(!$frame) self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		
		$nodes = $nio->gsnNodesByType($frame->type);
		
		$nodelist = self::nodelistFromNodes($nodes);
		$list = new \SpringDvs\FrameNetwork($nodelist);
		return self::forgePacket(SpringDvs\DvspMsgType::gsn_response_network, $list);
	}

	private static function processFrameResolution(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$frame = $packet->contentAs(SpringDvs\FrameResolution::contentType());
		if(!$frame) self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		
		$response = Resolution::resolveUrl($frame->url, $nio);
		if(!$response) return self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		
		return $response;
	}
	
	private static function processFrameGtnRegister(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$frame = $packet->contentAs(SpringDvs\FrameGtnRegistration::contentType());
		if(!$frame) return self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);

		$gsn = SpringDvs\Node::geosubFromNodeRegister($frame->nodereg);
		if(!$gsn) return self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		
		$node = SpringDvs\Node::from_nodestring($frame->nodereg);
		if(!self::remoteIsRoot($gsn, $nio)) {
			return self::rcodePacket(\SpringDvs\DvspRcode::network_error);
		}

		if($frame->register) {
			if(!$nio->gtnGeosubRegister($node, $gsn))
				return self::rcodePacket(\SpringDvs\DvspRcode::netspace_error);
			return self::rcodePacket(\SpringDvs\DvspRcode::ok);
		} else {
			if(!$nio->gtnGeosubUnregister($node, $gsn))
				return self::rcodePacket(\SpringDvs\DvspRcode::netspace_error);
			return self::rcodePacket(\SpringDvs\DvspRcode::ok);
		}
	}
	
	private static function processFrameGeosub(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$f = $packet->contentAs(SpringDvs\FrameGeosub::contentType());
		if(!$f) return self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);

		$nodes = $nio->gtnGeosubRootNodes($f->geosub);
		$frame = new \SpringDvs\FrameNetwork(self::nodelistFromNodes($nodes));
		return self::forgePacket(SpringDvs\DvspMsgType::gsn_response_network, $frame); 
	}
	
	private static function processRequest(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		$url = $packet->contentAs(SpringDvs\FrameResolution::contentType());
		return RequestHandler::process($url->url);
	}
	

	private static function processFrameUnitTest(\SpringDvs\DvspPacket &$packet, NetspaceKvs &$nio) {
		if(!\SpringDvs\Config::$spec['testing'])
			return self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		
		$f = $packet->contentAs(SpringDvs\FrameUnitTest::contentType());
		if(!$f) return self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);	
		
		switch($f->action) {
		case \SpringDvs\UnitTestAction::reset:
			reset_live_env($nio);
			break;
		case \SpringDvs\UnitTestAction::update_address:
			update_address_live_env($nio, $f->extra);
			break;
		case \SpringDvs\UnitTestAction::add_geosub_root:
			add_geosub_root_live_env($nio, $f->extra);
			break;
		return self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
		}
		
		return self::rcodePacket(\SpringDvs\DvspRcode::ok);
	}

	public static function nodelistFromNodes($nodes) {
		$nodelist = "";
		foreach($nodes as $node) {
			$nodelist .= $node->toNodeString() .";";
		}
		
		return $nodelist;
	}
	
	public static function forgePacket($type, \SpringDvs\iFrame& $frame) {
		return \SpringDvs\DvspPacket::ofType($type, $frame->serialise());
	}

	private static function remoteIsRoot($geosub, NetspaceKvs &$nio) {
		$addr = \SpringDvs\Node::addressFromString($_SERVER['REMOTE_ADDR']);
		$roots = $nio->gtnGeosubRootNodes($geosub);
		foreach($roots as $root) {
			if($root->address() == $addr)
				return true;
		}
		
		return false;
	}
}
