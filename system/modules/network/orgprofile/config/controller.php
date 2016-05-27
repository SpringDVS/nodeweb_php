<?php
if(!defined('NODE_ADMIN')) die();

use Flintstone\Flintstone;

class ServiceController {
	private $db;
	public function __construct() {
		$options['dir'] = SpringDvs\Config::$sys['store_live'];
		$this->db = new Flintstone('netservice_orgprofile', $options);
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
	
	private function updatePost() {
		$input = [
			'name' => filter_input(INPUT_POST, 'name'),
			'website' => filter_input(INPUT_POST, 'website'),
			'tags' => filter_input(INPUT_POST, 'tags')
		];

		$this->db->set('profile', $input);
		return json_enode(['result' => 'ok']);
	}
	
	private function remPost() {
		$key = filter_input(INPUT_POST, 'key');
		$this->db->delete($key);
		return json_enode(['result' => 'ok']);
	}
	
	private function profileGet() {
		$profile = $this->db->get('profile');
		
		return json_encode($profile);
	}
}

