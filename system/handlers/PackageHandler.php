<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class PackageHandler implements IPackageHandler {
	private $repo;
	
	public function __construct($repo = 'http://packages.spring-dvs.org') {
		$this->repo = $repo;
	}
	
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
	
	public function validate($archive, $checksum) {
		$digest = sha1_file($archive);
		if($digest != $checksum) return false;
		return true;
	}
	
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
	
	public function unlink($archive) {
		unlink($archive);
	}
}