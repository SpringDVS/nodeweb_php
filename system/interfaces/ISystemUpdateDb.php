<?php

interface ISystemUpdateDb {
	public function add($prefix, $services);
	public function services($prefix);
	public function delete($prefix);
	
	public function lastTimestamp();
	public function resetTimestamp();
}