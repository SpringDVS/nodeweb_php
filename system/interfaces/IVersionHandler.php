<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Provides an interface for checking against version information
 */
interface IVersionHandler {
	
	/**
	 * Retrieve version information on a particular package
	 *
	 * @param string $pkg The package name
	 * @return associative array of version information
	 */
	public function info($pkg);
	
	/**
	 * Checks if a package needs updating based on the version string
	 *
	 * @param string $strLocalVersion The local version string of package
	 * @param string $strRemoteVersion The remote version string of package
	 * @return true if needs updating; otherwise false
	 */
	public function needsUpdate($strLocalVersion, $strRemoteVersion);
}