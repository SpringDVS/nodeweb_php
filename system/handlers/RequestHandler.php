<?php
use SpringDvs\DvspPacket;

/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * This is for handling internal network service requests
 * 
 * These requests are performed across the Spring network by other nodes
 * on the network. They are requests on a particular service as specified
 * by the Spring network URI
 * 
 * example:
 * 
 *   spring://cci.esusx.uk/bulletin
 *   
 * This object handles the request at the point of a URI rather than the
 * entire DVSP packet
 *
 */
class RequestHandler {
	
	/**
	 * Process a URI request
	 * 
	 * @param string $urlstr The URI
	 * @return SpringDvs\DvspPacket The response packet
	 */
	public static function process($urlstr) {
		$url = new \SpringDvs\Url($urlstr);
		if($url->route()[0] != \SpringDvs\Config::$spec['springname']) {
			$frame = new \SpringDvs\FrameResponse(SpringDvs\DvspRcode::netspace_error);
			return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response, $frame->serialise()
			);
		}
		$rpath = $url->res();
		if(!$rpath) {
			$frame = new \SpringDvs\FrameResponse(SpringDvs\DvspRcode::malformed_content);
			return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response, $frame->serialise()
					);
		}
		$res = $rpath[0];
		$rpath = array_slice($rpath, 1);
	
		$path = "system/modules/network/$res/request.php";
		$ipath = "system/modules/network/$res/info.php";
		$lock =  "system/modules/network/$res/update.lock";

		if(!file_exists($path) || !file_exists($ipath) || file_exists($lock)) {
			$frame = new \SpringDvs\FrameResponse(SpringDvs\DvspRcode::malformed_content);
			return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response, $frame->serialise()
			);
		}
		
		$response = include "$path";
		$info = include "$ipath";
		$node = \SpringDvs\nodeurl_from_config();
		
		/*
		 * ToDo:
		 * This could be messagepack instead of JSON
		 */
		
		$out = $info['encoding'] == 'json' 
				? json_encode([$node => $response])
				: $response;

		return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response_high,
					$out
			);
	}
}
