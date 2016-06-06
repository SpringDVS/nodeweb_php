<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Provides a means of checking against version information
 * 
 *  The information is the latest released version as well as a checksum
 *  for the package. It is JSON encoded and stored on a separate system
 *  to the packages.
 *  
 *  The format for version information resource of a particular package is:
 *  
 *    {module-type}.{module}.json
 *    
 *  Assumes HTTP service
 */

class VersionHandler implements IVersionHandler {
	private $src;
	
	/**
	 * Constructor sets the default location for the version information
	 * 
	 * @param string $src The source of the version information
	 */
	public function __construct($src = 'http://spring-dvs.org/versions') {
		$this->src = $src;
	}

	/**
	 * Retrieve version information on a particular package
	 * 
	 * @param string $pkg The package name
	 * @return associative array of version information
	 */
	public function info($pkg) {
		try {
			return json_decode(file_get_contents("{$this->src}/{$pkg}.json"), true);
		} catch(Exception $e) { return null; }
	}
	
	/**
	 * Checks if a package needs updating based on the version string
	 * 
	 * @param string $strLocalVersion The local version string of package
	 * @param string $strRemoteVersion The remote version string of package
	 * @return true if needs updating; otherwise false
	 */
	public function needsUpdate($strLocalVersion, $strRemoteVersion) {
		$local = new SemanticVersion($strLocalVersion);
		return $local->lessThan(new SemanticVersion($strRemoteVersion));
	}
}