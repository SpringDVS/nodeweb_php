<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * A detailed representation of a semantic version string
 */
class SemanticVersion {
	public $major;
	public $minor;
	public $patch;
	public $prerelease;
	
	/**
	 * Build object from version string. Valid versions:
	 * - 1
	 * - 1.0
	 * - 1.0.1
	 * - 1.0.1-rc1
	 * 
	 * @param string $vstring Version string
	 */
	public function __construct($vstring) {
		$sections = explode('-', $vstring);
		$versions = explode('.', $sections[0]);

		$this->major = isset($versions[0]) ? intval($versions[0]) : 0;
		$this->minor = isset($versions[1]) ? intval($versions[1]) : 0;
		$this->patch = isset($versions[2]) ? intval($versions[2]) : 0;
		$this->prerelease =  isset($sections[1]) ? $sections[1] : "";
	}
	
	/**
	 * Check if this object is a greater version than one provided
	 * @param SemanticVersion $thisVersion Version to check against
	 */
	public function greaterThan(SemanticVersion $thisVersion) {
		if($this->major > $thisVersion->major) { return true; }
		if($this->minor > $thisVersion->minor) { return true; }
		if($this->patch > $thisVersion->patch) { return true; }
		return false;
	}
	
	/**
	 * Check if this object is a lesser version than one provided
	 * @param SemanticVersion $thisVersion Version to check against
	 */
	public function lessThan(SemanticVersion $thisVersion) {
		if($this->major < $thisVersion->major) { return true; }
		if($this->minor < $thisVersion->minor) { return true; }
		if($this->patch < $thisVersion->patch) { return true; }
		return false;
	}

	/**
	 * Check if this object of equal version than one provided
	 * @param SemanticVersion $thisVersion Version to check against
	 */
	public function same(SemanticVersion $thisVersion) {
		if($this->major != $thisVersion->major) { return false; }
		if($this->minor != $thisVersion->minor) { return false; }
		if($this->patch != $thisVersion->patch) { return false; }
		return true;
	}
}
