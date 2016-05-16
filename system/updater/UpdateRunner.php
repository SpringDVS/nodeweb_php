<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class UpdateRunner {
	public static function serviceNetwork($module, $info) {
		return self::run('nws', 'network', $module, $info);
	}
	
	public static function serviceGateway($module, $info) {
		return self::run('gws', 'gateway', $module, $info);
	}
	
	private static function run($prefix, $type, $module, $info) {
		$path = \SpringDvs\Config::$sys['store']."/cache";
		$package = "$prefix.{$module}_{$info['version']}.tgz";
		$pkgpath = "$path/$package";
		if(!file_exists($pkgpath)) {
			if(file_put_contents($pkgpath, fopen("http://packages.spring-dvs.org/{$package}", 'r')) === false) {
				return -2;
			}
		}
		
		if(!self::validatePackage($pkgpath, $info)) {
			unlink($pkgpath);
			return -1;
		}
		self::lock($type, $module);
		$arch = "$path/$prefix.{$module}_{$info['version']}.tar";
		$p = new PharData($pkgpath);
		$p->decompress("{$module}_{$info['version']}.tar");
		$phar = new PharData($arch);
		$extractionPath = "system/modules/$type/";
		$phar->extractTo($extractionPath, null, true);
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
	
	private static function validatePackage($filename, $info) {
		$digest = sha1_file($filename);
		if($digest != $info['sha1']) return false;
		return true;
	}
}
