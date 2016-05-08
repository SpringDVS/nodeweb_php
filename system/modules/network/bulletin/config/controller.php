<?php
if(!defined('NODE_ADMIN')) die();

use Flintstone\Flintstone;

class ServiceController {
	private $db;
	public function __construct() {
		$options['dir'] = SpringDvs\Config::$sys['store_live'];
		$this->db = new Flintstone('netservice_bulletin', $options);
	}
	public function request($action) {
		$task = ($t = filter_input(INPUT_GET, 'task')) ?
				$t : "none";
		
		
		$method = $task.$action;
		
		
		if(!method_exists('ServiceController', $method)) {
			return json_encode([]);
		}
		
		return $this->$method();
	}
	
	private function newPost() {
		return json_encode(['note' => 'ok']);
	}
}

