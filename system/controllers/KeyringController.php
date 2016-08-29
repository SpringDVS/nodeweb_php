<?php

class KeyringController {
	private $_handler;

	public function __construct() {
		$this->_handler = new KeyringHandler();
	}

	public function request($ref, $method) {
		if($ref == "all") {
			return $this->getPublicKeyring();
		} else if($ref == "private") {
			$call = "private".$method;
			return $this->$call();
		} else if($ref == "import") {
			return $this->importPost();
		} else if($ref == "remove") {
			return $this->removePublicKey();
		} else if($ref == "generate") {
			return $this->generateKey();
		}
		
		if($method == "Get") {
			return $this->getPublicKey($ref);
		}
		
		return json_encode(['result' => 'error']);
	}

	private function getPublicKeyring() {
		
		return json_encode($this->_handler->getPublicKeyring());
	}

	private function getPublicKey($id) {
		$key = $this->_handler->getKey($id);
		
		if(!$key)return json_encode(['result' => 'error']);
		
		return json_encode(['result' => 'ok', 'key' => $key]);
	}

	private function privateGet() {
		$key = $this->_handler->getNodePrivateKey();
		if(!$key) return json_encode(['result' => 'error', 'msg' => "Not found"]);
		
		
		return json_encode(['result' => 'ok', 'key' => $key]);
	}

	private function privatePost() {
		
	}

	private function importPost() {
		$armor = filter_input(INPUT_POST, "armor");
		if(!$armor) return json_encode(['result' => 'error']);
		$result = $this->_handler->importPublicKey($armor);
		if(!$result) return json_encode(['result' => 'error','msg' => "On Request"]);
		return json_encode(['result' => 'ok','name' => $result]);
	}

	private function removePublicKey() {
		$id = filter_input(INPUT_GET, "keyid");
		if(!$id) return json_encode(['result' => 'error']);
		
		$this->_handler->removeKey($id);
		return json_encode(['result' => 'ok']);
	}

	private function generateKey() {
		$passphrase = filter_input(INPUT_POST, 'passphrase');
		$name = \SpringDvs\Config::$spec['springname'] 
				. '.' . \SpringDvs\Config::$net['geosub']
				. '.' . \SpringDvs\Config::$net['geotop'];
		
		$email = filter_input(INPUT_POST, 'email');

		if(!$this->_handler->generateKey($name, $email, $passphrase))
			return json_encode(['result' => 'error']);

		return json_encode(['result' => 'ok']);
	}
}