<?php


interface IPackageHandler {
	public function pull($pkg);
	public function validate($archive, $checksum);
	public function unpack($path, $archive, $dest);
}