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
		$title = filter_input(INPUT_POST, 'title');
		$uid = sha1($title+time());
		$input = [
			'title' => $title,
			'content' => filter_input(INPUT_POST, 'content'),
			'uid' => $uid,
			'tags' => filter_input(INPUT_POST, 'tags')
		];

		$this->db->set((int)(time()/10), $input);
		return json_encode(['result' => 'ok']);
	}
	
	private function remPost() {
		$key = filter_input(INPUT_POST, 'key');
		$this->db->delete($key);
		return json_enode(['result' => 'ok']);
	}
	
	private function allGet() {
		$bulletins = $this->db->getAll();
		$out = [];
		
		foreach($bulletins as $key => $val) {
			$tmp = ['key' => $key];
			$tmp = array_merge($tmp, $val);
			$out[] = $tmp;
		}
		return json_encode($out);
	}
}

