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
	public static function process($url) {
		
		if($url->route()[0] != \SpringDvs\Config::$spec['springname']) {
			return "101";
		}
		$rpath = $url->res();
		if(!$rpath) {
			return "104";
		}
		$res = $rpath[0];
		$rpath = array_slice($rpath, 1);
	
		$path = "system/modules/network/$res/request.php";
		$ipath = "system/modules/network/$res/info.php";
		$lock =  "system/modules/network/$res/update.lock";

		if(!file_exists($path) || !file_exists($ipath) || file_exists($lock)) {
			return "122"; // Unsupported service
		}
		
		$response = include "$path";
		$info = include "$ipath";
		$node = \SpringDvs\nodeurl_from_config();
		
			
		$out = $info['encoding'] == 'json' 
				? '/text '.json_encode([$node => $response])
				: $response;
		$len = strlen($out)+7;
		return '200 ' .$len. ' service' . $out;
	}
}
