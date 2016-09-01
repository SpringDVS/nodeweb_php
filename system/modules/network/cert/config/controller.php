<?php
if(!defined('NODE_ADMIN')) die();

use Flintstone\Flintstone;

class ServiceController {
	private $_db;
	private $_options;

	public function __construct() {
		$options['dir'] = SpringDvs\Config::$sys['store_live'];
		$this->_db = new Flintstone('netservice_cert', $options);
		$this->_options = $this->_db->get('options');
		if(!$this->_options) {
			$this->_options = array(
					'accept_push' => false,
			);
			$this->_db->set('options', $this->_options);
		}
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
	
	private function certGet() {
		$handler = new KeyringHandler();
		$cert = $handler->getNodePublicKey();
		
		if(!$cert) return array('result' => 'error');
		
		return json_encode(array('result' => 'ok', 'cert' => $cert));
	}
	
	private function optionsGet() {
		return json_encode(array('result' => 'ok', 'options' => $this->_options));
	}
	
	private function optionsPost() {
		$accept_push = filter_input(INPUT_POST, 'accept_push') == 1 
							? true : false;
		
		$this->_options['accept_push'] = $accept_push;
		
		$this->_db->set('options', $this->_options);
		return json_encode(array('result' => 'ok'));
	}
	
}

