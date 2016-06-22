<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */


/**
 * Object used to perform update action on the system or modules
 * 
 * The action downloads the latest package archives queued for updating,
 * verifies the archives and unpacks them into the system
 * 
 * Warning: This object performs destructive routines
 *
 */
class UpdateRunner {
	private $ph;
	const FAIL_DOWNLOAD = -2;
	const FAIL_CHECKSUM = -1;
	const OK = 0;
	
	
	/**
	 * Constructors assigns the package handler for the system
	 * 
	 * @param IPackageHandler $pkgHandler
	 */
	public function __construct(IPackageHandler $pkgHandler) {
		$this->ph = $pkgHandler;
	}
	
	/**
	 * Update a network service module
	 * 
	 * @param string $module The name of the module
	 * @param array $info Update information
	 */
	public function serviceNetwork($module, $info) {
		return $this->run('nws', 'network', $module, $info);
	}
	
	/**
	 * Update a gateway service module
	 *
	 * @param string $module The name of the module
	 * @param array $info Update information
	 */
	public function serviceGateway($module, $info) {
		return $this->run('gws', 'gateway', $module, $info);
	}
	
	/**
	 * Update the core
	 * 
	 * @param array $info Core update information
	 */
	public function core($info) {
		$package  = "php.web.core_{$info['version']}.tgz";
		$path = $this->pull($package, $info['sha1']);
		
		if($path == self::FAIL_DOWNLOAD || $path == self::FAIL_CHECKSUM) {
			return $path;
		}
		
		$this->ph->unpack($path, './');
		return self::OK;
	}
	
	/**
	 * Run the full update action for service module
	 * 
	 * @param string $prefix Prefix of the service
	 * @param string $type The type of service
	 * @param string $module The name of the
	 * @param array $info Update information
	 * @return OK on success; FAIL_DOWNLOAD on failure to download; FAIL_CHECKSUM on invalid package
	 */
	private function run($prefix, $type, $module, $info) {

		$package = "$prefix.{$module}_{$info['version']}.tgz";		


		$path = $this->pull($package, $info['sha256']);
		if($path == self::FAIL_DOWNLOAD || $path == self::FAIL_CHECKSUM) {
			return $path;
		}

		$this->lock($type, $module);
		$this->ph->unpack($path, "system/modules/$type");
		$this->unlock($type, $module);
		return self::OK;		
	}
	
	/**
	 * Lock a module for updating
	 * 
	 * @param string $type The module type
	 * @param string $module The name of the module
	 */
	private function lock($type, $module) {
		fopen("system/modules/$type/$module/update.lock", 'w');
	}
	
	/**
	 * Unlock a module for updating
	 *
	 * @param string $type The module type
	 * @param string $module The name of the module
	 */
	private function unlock($type, $module) {
		unlink("system/modules/$type/$module/update.lock");
	}

	/**
	 * Download and verify a package from the repository
	 *
	 * @param string $package The name of the package
	 * @param string $check The checksum to verify the package
	 */
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
