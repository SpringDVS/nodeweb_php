<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * The protocol handler for processing messages as a SpringDVS node
 * and sometimes a root node if it has been elevated (elevation is not
 * yet active)
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
	public static function process($request) {
		try {
			$msg = \SpringDvs\Message::fromStr($request);
		} catch(Exception $e) {
			return "104"; // MalformedContent
		}
		
		$nio = new NetspaceKvs(false);
		
		switch($msg->cmd()) {
			case \SpringDvs\CmdType::Service: return ProtocolHandler::actionService($msg, $nio);
			default: return "121"; // UnsupportedAction
		}
	}
	
	private static function actionService(\SpringDvs\Message $msg) {
		$url = $msg->content()->uri();
		return RequestHandler::process($url);
	}
	
}
