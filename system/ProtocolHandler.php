<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * The protocol handler for processing packets as a SpringDVS node
 * and sometimes a root node if it has been elevated
 *
 * @author cfg
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
		
		$packet = SpringDvs\DvspPacket::deserialise($bytes);
		if(!$packet) {
			$response = self::rcodePacket(\SpringDvs\DvspRcode::malformed_content);
			return $response->serialise();
		}
		
		switch($packet->header()->type) {
	
		default:
			return self::rcodePacket(\SpringDvs\DvspRcode::ok)->serialise();
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
}
