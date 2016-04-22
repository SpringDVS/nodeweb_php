<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SpringApiController
 *
 * @author cfg
 */
class SpringApiController {
	public function request() {
		$request = Flight::request()->getBody();
		$in = SpringDvs\hex_to_bin($request);
		$out = ProtocolHandler::processBytes($in);
		
		return bin2hex($out);
		
	}
}
