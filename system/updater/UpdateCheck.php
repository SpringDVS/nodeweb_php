<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

use Flintstone\Flintstone;

class UpdateCheck {
	private $db;
	public function __construct() {
		$options['dir'] = SpringDvs\Config::$sys['store_live'];
		$this->db = new Flintstone('system', $options);
	}
	public function check($force = false) {
		if(!$force && !$this->timeout()){
			return;
		}

		$this->versions();
	}
	
	private function timeout() {
		$t = $this->db->get('last_check');
		if(!$t){ $this->db->set('last_check', time()); }
		
		$c = time();
		$diff = $c - $t; 
		if($diff < 5400){ return false; }

		$this->db->set('last_check', $c);
		return true;
	}
	
	private function versions() {
		include_once __DIR__.'/SemanticVersion.php';
		
		
		foreach(array('nws' => 'network', 'gws' => 'gateway') as $prefix => $type) {
			$services = array();
		
			foreach($this->extractServices("system/modules/$type/") as $info) {
				$json = "";
				try {
					$json = file_get_contents("http://spring.care-connections.org/versions/{$prefix}.{$info['module']}.json");
				} catch(Exception $e) { continue; }
				
				if(!$json){ continue; }

				$response = json_decode($json, true);
				
				if(!$this->needsUpdate($info['version'], $response['version'])) {
					continue;
				}
				$services[$info['module']] = $response;

			}
			$this->db->delete($prefix);
			$this->db->set($prefix, $services);	
		}
		
	}
	
	public function getUpdateQueue() {
		$queue = array();
		if( ($q = $this->db->get('nws')) ) {
			$queue['nws'] = $q;
		} else {
			$queue['nws'] = array();
		}
		if( ($q = $this->db->get('gws')) ) {
			$queue['gws'] = $q;
		} else {
			$queue['gws'] = array();
		}
		
		return $queue;
	}
	
	private function extractServices($root) {
		$dirs = array_filter(glob($root.'/*'), 'is_dir');
		$result = array();
		foreach($dirs as $d) {
			$path = $d.'/info.php';
			
			if(!file_exists($path)) continue;
			$r = include $path;
			if(!is_array($r)) continue;
			$result[] = $r;
		}
		return $result;
	}
	
	private function needsUpdate($localStr, $remoteStr) {
			$local = new SemanticVersion($localStr);
			$remote = new SemanticVersion($remoteStr);

			return $local->lessThan($remote);
	}
}
