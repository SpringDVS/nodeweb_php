<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of VersionWrap
 *
 * @author cfg
 */
class SemanticVersion {
	public $major;
	public $minor;
	public $patch;
	public $prerelease;
	
	public function __construct($vstring) {
		$sections = explode('-', $vstring);
		$versions = explode('.', $sections[0]);

		$this->major = isset($versions[0]) ? intval($versions[0]) : 0;
		$this->minor = isset($versions[1]) ? intval($versions[1]) : 0;
		$this->patch = isset($versions[2]) ? intval($versions[2]) : 0;
		$this->prerelease =  isset($sections[1]) ? $sections[1] : "";
	}
	
	public function greaterThan(SemanticVersion $thisVersion) {
		if($this->major > $thisVersion->major) { return true; }
		if($this->minor > $thisVersion->minor) { return true; }
		if($this->patch > $thisVersion->patch) { return true; }
		return false;
	}

	public function lessThan(SemanticVersion $thisVersion) {
		if($this->major < $thisVersion->major) { return true; }
		if($this->minor < $thisVersion->minor) { return true; }
		if($this->patch < $thisVersion->patch) { return true; }
		return false;
	}

	public function same(SemanticVersion $thisVersion) {
		if($this->major != $thisVersion->major) { return false; }
		if($this->minor != $thisVersion->minor) { return false; }
		if($this->patch != $thisVersion->patch) { return false; }
		return true;
	}
}
