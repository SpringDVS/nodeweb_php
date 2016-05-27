<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class UpdateRunner {
	private $db;
	private $ph;
	
	public function __construct(ISytemUpdateDb $db,
								IPackageHandler $pkgHandler
	) {
		$this->db = $db;
		$this->ph = $pkgHandler;
	}
	public function serviceNetwork($module, $info) {
		return $this->run('nws', 'network', $module, $info);
	}
	
	public function serviceGateway($module, $info) {
		return $this->run('gws', 'gateway', $module, $info);
	}
	
	public static function core($info) {
		
	}
	
	private function run($prefix, $type, $module, $info) {

		$package = "$prefix.{$module}_{$info['version']}.tgz";		
		$pkgpath = $this->ph->pull($package);
		if(!$this->ph->validatePackage($pkgpath, $info['sha1'])) {
			unlink($pkgpath);
			return -1;
		}	
		
		self::lock($type, $module);
		$arch = "$path/$prefix.{$module}_{$info['version']}.tar";
		
		$ph->unpack($arch, "system/modules/$type/");
		self::unlock($type, $module);
		
		unlink($pkgpath);
		unlink($arch);
		return 0;		
	}
	
	private static function lock($type, $module) {
		fopen("system/modules/$type/$module/update.lock", 'w');
	}
	private static function unlock($type, $module) {
		unlink("system/modules/$type/$module/update.lock");
	}
}
