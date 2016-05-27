<?php

class ModuleHandler implements IModuleHandler {
	
	public function getInfoList($type) {
		$dirs = array_filter(glob($type.'/*'), 'is_dir');
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
	
	public function isValid($type, $module) {
		$root = "system/modules/$type/$res";
		$path = "$root/request.php";
		$ipath = "$root/info.php";
		$lock =  "$root/update.lock";
	}
	
	public function install($type, $module, $archive) {

	}

	public function upgrade($type, $module, $archive) {
		;
	}
}
