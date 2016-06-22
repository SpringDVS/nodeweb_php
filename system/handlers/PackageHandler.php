<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Object for handling download and installation of packages
 * 
 * Packages can be updates to the core or to modules or new modules to install.
 * The packages in the repository take the form of:
 * 
 *   {module-type}.{module}_{vmj}.{vmi}.{vpt}.tgz
 *   
 * The object assumes HTTP service 
 */
class PackageHandler implements IPackageHandler {
	private $repo;
	
	/**
	 * Constructor provides repository to search
	 * 
	 * @param string $repo The URI of the repository (assumes HTTP)
	 */
	public function __construct($repo = 'http://packages.spring-dvs.org') {
		$this->repo = $repo;
	}
	
	/**
	 * Pull a package archive from the repository
	 * 
	 * @param string $pkg The name of the package
	 * @return string path to downloaded archive; otherwise null on error
	 */
	public function pull($pkg) {
		$path = \SpringDvs\Config::$sys['store']."/cache";
		$pkgpath = "$path/$pkg";
		if(!file_exists($pkgpath)) {
			if(file_put_contents($pkgpath, fopen("{$this->repo}/{$pkg}", 'r')) === false) {
				return null;
			}
		}
		
		return $pkgpath;
	}
	
	/**
	 * Validate the package against a checksum
	 * 
	 * Currently checks against SHA1
	 * 
	 * @param string $archive Path to archive
	 * @param string $checksum The checksum to check against
	 * @return true if valid; otherwise false
	 */
	public function validate($archive, $checksum) {
		$digest = hash_file('sha256', $archive);
		if($digest != $checksum) return false;
		return true;
	}
	
	/**
	 * Unpack the package into the system (Warning: destructive)
	 * 
	 * @param string $archive Path to archive
	 * @param string $dest Path of where to extract the package
	 */
	public function unpack($archive, $dest) {
		$p = new PharData($archive);
		$path = dirname($archive);
		$tname = $fname = explode('.', basename($archive));
		unset($fname[0]);
		unset($fname[count($fname)]);
		unset($tname[count($tname)-1]);
		
		$tar = implode('.',$fname).".tar";
		$utar = implode('.', $tname);
		
		
		try{
			$dephar = $p->decompress($tar);
			$phar = new PharData("$path/$utar.tar");
			$dephar->extractTo($dest, null, true);
			unlink("$path/$utar.tar");
		} catch (Exception $e) {
			
			echo $e;
			return;
		}
		
		unlink($archive);
	}
	
	/**
	 * Delete an archive file (for consistancy)
	 * 
	 * @param string $archive Path to archive
	 */
	public function unlink($archive) {
		unlink($archive);
	}
}