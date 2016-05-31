<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class VersionHandler implements IVersionHandler {
	private $src;
	
	public function __construct($src = 'http://spring.care-connections.org/versions') {
		$this->src = $src; 
	}

	public function info($pkg) {
		try {
			return json_decode(file_get_contents("{$this->src}/{$pkg}.json"), true);
		} catch(Exception $e) { return null; }
	}
	
	public function needsUpdate($strLocalVersion, $strRemoteVersion) {
		$local = new SemanticVersion($strLocalVersion);
		return $local->lessThan(new SemanticVersion($strRemoteVersion));
	}
}