<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

use Flintstone\Flintstone;

/**
 * Provides Key-Value Store implementation of ISystemUpdateDb
 * 
 * Provides an interface for handling the data surrounding updates
 */
class SystemUpdateKvs implements ISystemUpdateDb {
	private $db;
	
	/**
	 * Constructor initialised Flintstone
	 */
	public function __construct() {
		$options['dir'] = SpringDvs\Config::$sys['store_live'];
		$this->db = new Flintstone('system', $options);
	}

	/**
	 * Add a list of packages that need updating
	 *
	 * @param string $prefix The prefix of the package (core|gws|nsw)
	 * @param array $services Version information of outdated packages
	 */
	public function add($prefix, $services) {
		return $this->db->set($prefix, $services);
	}

	/**
	 * Clear the prefix of any stored package information
	 * @param strimg $prefix
	 * @return true on successful deletion; otherwise false
	 */
	public function delete($prefix) {
		return $this->db->delete($prefix);
	}

	/**
	 * Get the list of services that need updating within a prefix
	 *
	 * @param string $prefix The prefix of the package (core|gws|nsw)
	 * @return array A list of service information arrays
	 */
	public function services($prefix) {
		return $this->db->get($prefix);
	}

	/**
	 * Timestamp of last update check
	 */
	public function lastTimestamp() {
		return $this->db->get('last_check');
	}

	/**
	 * Reset the timestamp to current time
	 */
	public function resetTimestamp() {
		return $this->db->set('last_check', time());
	}
}