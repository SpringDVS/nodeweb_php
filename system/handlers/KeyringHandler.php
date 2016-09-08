<?php
use Flintstone\Flintstone;
if(!defined('NODE_ADMIN') && !defined('NODE_KEYGEN') && !defined('CERT_REQ')) die();

class KeyringHandler implements IKeyring {
	private $_keyring;
	private $_remote;
	
	public function __construct() {

		$options['dir'] = SpringDvs\Config::$sys['store_live'];
		$this->_keyring = new Flintstone('keyring', $options);
		$this->_remote = "https://pkserv.spring-dvs.org";
	}

	public function getNodePublicKey() {
		$key = $this->_keyring->get('this');
		return $key['public'];
	}
	
	public function getNodePrivateKey() {
		$key = $this->_keyring->get('this');
		return $key['private'];
	}
	
	public function setNodePrivateKey($key) {
		$obj = $this->_keyring->get('this');
		if(isset($obj['private'])) return false;
		$obj['private'] = $key;
		$this->_keyring->set('this', $obj);
		
		return true;
	}
	
	public function setNodePublicKey($key) {
		$obj = $this->_keyring->get('this');
	
		$json = $this->requestImport($key);
		$pub = json_decode($json, true);
		
		$obj['public'] = $pub;
		$this->_keyring->set('this', $obj);
		$this->addKey($pub, $key);
		return true;
	}
	
	public function getKey($id) {
		$key = $this->_keyring->get($id);
		$nodeKey = $this->getNodeKeyid();
		
		if($id == 'this') {
			$key = $key['public'];
		}
		
		$signed = false;
		
		for($i = 0; $i < count($key['sigs']); $i++) {
			$keyid = $key['sigs'][$i];
			$key['sigs'][$i] = [$keyid, $this->getUserId($keyid)];
			
			if($keyid == $nodeKey) {
				$signed = true;
			}	
		}
		
		$key['signed'] = $signed;
		
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

		$result = $this->requestImport($armor);
		$key = json_decode($result, true);


		// Check if we need to update a key we already have
		$subject = $this->_keyring->get($key['keyid']);
		if($subject) {
			/* We have the key already so need to import
			 * against the key in the keyring instead of
			 * doing a straight import
			 */
			$result = $this->requestImport($armor, $subject['public']);
			$key = json_decode($result, true);
			$armor = $key['armor'];
		}


		$this->addKey($key, $armor);
		$chk = $this->_keyring->get('this');
		if($chk && $key['keyid'] == $chk['public']['keyid']) {
			$chk['public'] = $key;
			$this->_keyring->set('this', $chk);
		}
		
		return $key['name'];
	}

	public function removeKey($id) {
		$this->_keyring->delete($id);
	}
	
	public function signCertificate($id, $passphrase) {

		$public = $this->getKeyArmor($id);
		$private = $this->getNodePrivateKey();

		$body = "SIGN
$passphrase
PUBLIC {
$public
}
PRIVATE {
$private
}\n";
		
		$json = $this->request($body);
		$obj = json_decode($json,true);
		if(!$obj) return false;
		
		if($public == $obj['public']) {
			return false;
		}
		usleep(250000);
		$this->importPublicKey($obj['public']);

		return true;
	}
	
	public function generateKey($name, $email, $passphrase) {
		$body = "KEYGEN
$passphrase
$name
$email\n";
		$result = $this->request($body);
		$obj = json_decode($result, true);
		
		$armor = $obj['public'];
		$json = $this->requestImport($armor);
		$pub = json_decode($json, true);

		$obj['public'] = $pub;
		$this->_keyring->set('this', $obj);
		$this->addKey($pub, $armor);
		return true;
	}
	
	private function request($body) {
		$ch = curl_init($this->_remote);
		
		//curl_setopt($ch, CURLOPT_URL,            $address);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_USERAGENT,      "WebSpringDvs/0.9" );
		curl_setopt($ch, CURLOPT_POSTFIELDS,      $body);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
				'User-Agent: WebSpringDvs/0.9'));
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
	
	private function requestImport($armor, $subject = null) {
		$body = "IMPORT
PUBLIC {
$armor
}\n";	

		if($subject) {
			$body .= "SUBJECT {\n$subject\n}\n";
		}
		return $this->request($body);
	}
	
	private function addKey($key, $armor) {
		$this->_keyring->set($key['keyid'], array(
				'public' => $armor,
				'name' => $key['name'],
				'email' => $key['email'],
				'sigs' => $key['sigs']
		));		
	}
	
	private function getNodeKeyid() {
		$key = $this->getNodePublicKey();
		
		if(!$key || !isset($key['keyid'])) {
			return null;
		}
		return $key['keyid'];
	}
	
	private function getKeyArmor($id) {
		$key = $this->_keyring->get($id);
		if(!$key) return null;
		
		return $key['public'];
	}
}
