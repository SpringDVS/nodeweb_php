<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */


/**
 * Provides functionality for working with the modules
 * 
 * This is pretty bare at the moment since modules are not particularly
 * sophosticated yet
 */
class ModuleHandler implements IModuleHandler {
	
	/**
	 * Get an array of all the module infos of a particular type
	 * 
	 * @param $type The string of type (network|gateway)
	 * @return array of module infos; otherwise an empty array 
	 */
	public function getInfoList($type) {
		$dirs = array_filter(glob(__DIR__.'/../modules/'.$type.'/*'), 'is_dir');

		$result = [];
		foreach($dirs as $d) {
			$path = $d.'/info.php';
			
			if(!file_exists($path)) continue;
			$r = include $path;
			if(!is_array($r)) continue;
			$result[] = $r;
		}

		return $result;
	}
	
	/**
	 * Check if module of type is in a valid state
	 * @param $type String of module type (network|gateway)
	 * @param $module String name of module
	 * @return true if valid; otherwise false
	 */
	public function isValid($type, $module) {
		$root = "system/modules/$type/$res";
		$path = "$root/request.php";
		$ipath = "$root/info.php";
		return true;
	}
}
