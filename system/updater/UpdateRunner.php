<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */



class UpdateRunner {
	private $ph;
	const FAIL_DOWNLOAD = -2;
	const FAIL_CHECKSUM = -1;
	const OK = 0;
	
	
	public function __construct(IPackageHandler $pkgHandler) {
		$this->ph = $pkgHandler;
	}
	public function serviceNetwork($module, $info) {
		return $this->run('nws', 'network', $module, $info);
	}
	
	public function serviceGateway($module, $info) {
		return $this->run('gws', 'gateway', $module, $info);
	}
	
	public function core($info) {
		$package  = "php.web.core_{$info['version']}.tgz";
		$path = $this->pull($package, $info['sha1']);
		
		if($path == self::FAIL_DOWNLOAD || $path == self::FAIL_CHECKSUM) {
			return $path;
		}
		
		$this->ph->unpack($path, './');
		return self::OK;
	}
	
	private function run($prefix, $type, $module, $info) {

		$package = "$prefix.{$module}_{$info['version']}.tgz";		


		$path = $this->pull($package, $info['sha1']);
		if($path == self::FAIL_DOWNLOAD || $path == self::FAIL_CHECKSUM) {
			return $path;
		}

		$this->lock($type, $module);
		$this->ph->unpack($path, "system/modules/$type/");
		$this->unlock($type, $module);
		return self::OK;		
	}
	
	private function lock($type, $module) {
		fopen("system/modules/$type/$module/update.lock", 'w');
	}
	private function unlock($type, $module) {
		unlink("system/modules/$type/$module/update.lock");
	}
	
	private function pull($package, $checksum) {
		$path = $this->ph->pull($package);
		if($path == null) return self::FAIL_DOWNLOAD;
		
		if(!$this->ph->validate($path, $checksum)) {
			$this->ph->unlink($path);
			return self::FAIL_CHECKSUM;
		}
		
		return $path;
	}
}
