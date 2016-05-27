<?php
use Flintstone\Flintstone;

class SystemUpdateKvs implements ISystemUpdateDb {
	private $db;
	
	public function __construct() {
		$options['dir'] = SpringDvs\Config::$sys['store_live'];
		$this->db = new Flintstone('system', $options);
	}

	public function add($prefix, $services) {
		return $this->db->set($prefix, $services);
	}

	public function delete($prefix) {
		return $this->db->delete($prefix);
	}
	
	public function services($prefix) {
		return $this->db->get($prefix);
	}
	
	public function lastTimestamp() {
		return $this->db->get('last_check');
	}
	
	public function resetTimestamp() {
		return $this->db->set('last_check', time());
	}
}