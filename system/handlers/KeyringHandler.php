<?php
use Flintstone\Flintstone;
if(!defined('NODE_ADMIN')) die();

class KeyringHandler implements IKeyring {
	private $_keyring;
	private $_remote;
	
	public function __construct() {

		$options['dir'] = SpringDvs\Config::$sys['store_live'];
		$this->_keyring = new Flintstone('keyring', $options);
		$this->_remote = "127.0.0.1:55500";
	}

	public function getNodePublicKey() {
		$key = $this->_keyring->get('this');
		return $key['public'];
	}
	
	public function getNodePrivateKey() {
		$key = $this->_keyring->get('this');
		return $key['private'];
	}
	
	public function setNodePrivateKey() {

	}
	
	public function setNodePublicKey() {

	}
	
	public function getKey($id) {
		$key = $this->_keyring->get($id);
		
		for($i = 0; $i < count($key['sigs']); $i++) {
			$keyid = $key['sigs'][$i];
			$key['sigs'][$i] = [$keyid, $this->getUserId($keyid)];
		}
		
		return $key;
	}
	
	public function getPublicKeyring() {
		$out = array();
		$keys = $this->_keyring->getAll();
		foreach($keys as $k => $v) {
			if(!isset($v['name'])) continue;
			$out[] = [$k, $v['name']];
		}
		
		return $out;
	}
	
	public function importPublicKey($armor) {
		$body = "IMPORT
PUBLIC {
$armor
}\n";
		
		$result = $this->request($body);
		$key = json_decode($result, true);

		
		$this->_keyring->set($key['keyid'], array(
			'public' => $armor,
			'name' => $key['name'],
			'email' => $key['email'],
			'sigs' => $key['sigs']
		));
		
		return $key['name'];
	}

	public function removeKey($id) {
		$this->_keyring->delete($id);
	}	
	
	private function request($body) {
		$ch = curl_init($this->_remote);
		
		//curl_setopt($ch, CURLOPT_URL,            $address);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_USERAGENT,           "WebSpringDvs" );
		curl_setopt($ch, CURLOPT_POSTFIELDS,      $body);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
				'User-Agent: WebSpringDvs/0.2'));
		$response = curl_exec($ch);
		
		if($response === false) {
			return false;
		}
		return $response;
	}
	
	private function getUserId($keyid) {
		$key = $this->_keyring->get($keyid);
		
		if(!$key) return "unknown";
		
		return $key['name'];
	}
	

}
