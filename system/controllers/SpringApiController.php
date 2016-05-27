<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class SpringApiController {
	public function request() {
		$request = trim(Flight::request()->getBody());
		
		$in = SpringDvs\hex_to_bin($request);
		define('SPRING_IF', true);
		$out = ProtocolHandler::processBytes($in);
		
		return \SpringDvs\bin_to_hex($out);
		
	}
}
