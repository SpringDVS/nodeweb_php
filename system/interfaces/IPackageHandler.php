<?php


interface IPackageHandler {
	public function pull($pkg);
	public function validate($archive, $checksum);
	public function unlink($archive);
	public function unpack($archive, $dest);
}