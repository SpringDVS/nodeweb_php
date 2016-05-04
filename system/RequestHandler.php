<?php

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
		
		$path = "system/modules/$res/request.php";
		
		if(!file_exists($path)) {
			$frame = new \SpringDvs\FrameResponse(SpringDvs\DvspRcode::malformed_content);
			return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response, $frame->serialise()
			);
		}
		
		$response = include "$path";
		return \SpringDvs\DvspPacket::ofType(
					\SpringDvs\DvspMsgType::gsn_response_high, $response
			);
	}
}
