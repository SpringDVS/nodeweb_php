<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Provides interface for Package Handler objects
 */
interface IPackageHandler {
	
	/**
	 * Pull a package archive from the repository
	 *
	 * @param string $pkg The name of the package
	 * @return string path to downloaded archive; otherwise null on error
	 */
	public function pull($pkg);
	
	/**
	 * Validate the package against a checksum
	 *
	 * @param string $archive Path to archive
	 * @param string $checksum The checksum to check against
	 * @return true if valid; otherwise false
	 */
	public function validate($archive, $checksum);
	
	/**
	 * Delete an archive file (for consistancy)
	 *
	 * @param string $archive Path to archive
	 */
	public function unlink($archive);
	
	/**
	 * Unpack the package into the system (could be destructive)
	 *
	 * @param string $archive Path to archive
	 * @param string $dest Path of where to extract the package
	 */
	public function unpack($archive, $dest);
}