<?php

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