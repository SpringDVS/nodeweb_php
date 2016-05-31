<?php

class PackageHandler implements IPackageHandler {
	private $repo;
	
	public function __construct($repo = 'http://packages.spring-dvs.org') {
		$this->repo = $repo;
	}
	
	public function pull($pkg) {
		$path = \SpringDvs\Config::$sys['store']."/cache";
		$pkgpath = "$path/$pkg";
		if(!file_exists($pkgpath)) {
			if(file_put_contents($pkgpath, fopen("{$this->repo}/{$package}", 'r')) === false) {
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
		$p->decompress(dirname($archive).'/unpacked.tar');
		$phar = new PharData($arch);
		$phar->extractTo($dest, null, true);
		unlink($path.'/unpacked.tar');
		unlink($archive);
	}
	
	public function unlink($archive) {
		unlink($archive);
	}
}