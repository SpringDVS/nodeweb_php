<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class RequestHandler {
	public static function process($urlstr) {
		$url = new \SpringDvs\Url($urlstr);
		if(end($url->route()) != \SpringDvs\Config::$spec['springname']) {
			$frame = new \SpringDvs\FrameResponse(SpringDvs\DvspRcode::netspace_error);
			return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response, $frame->serialise()
			);
		}
		$res = $url->res();
		
		$path = "system/modules/network/$res/request.php";
		
		if(!file_exists($path)) {
			$frame = new \SpringDvs\FrameResponse(SpringDvs\DvspRcode::malformed_content);
			return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response, $frame->serialise()
			);
		}
		
		$response = include "$path";
		
		$node = \SpringDvs\nodeurl_from_config();
		
		/*
		 * ToDo:
		 * This could be messagepack instead of JSON
		 */
		
		$json = json_encode(array($node => $response));
		return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response_high,
					$json
			);
	}
}
