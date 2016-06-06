<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Provides an interface for handling the data surrounding updates
 */
interface ISystemUpdateDb {
	
	/**
	 * Add a list of packages that need updating
	 * 
	 * @param string $prefix The prefix of the package (core|gws|nsw)
	 * @param array $services Version information of outdated packages
	 */
	public function add($prefix, $services);
	
	/**
	 * Get the list of services that need updating within a prefix
	 * 
	 * @param string $prefix The prefix of the package (core|gws|nsw)
	 * @return array A list of service information arrays
	 */
	public function services($prefix);
	
	/**
	 * Clear the prefix of any stored package information
	 * @param strimg $prefix
	 * @return true on successful deletion; otherwise false
	 */
	public function delete($prefix);
	
	/**
	 * Timestamp of last update check 
	 */
	public function lastTimestamp();
	
	/**
	 * Reset the timestamp to current time
	 */
	public function resetTimestamp();
}