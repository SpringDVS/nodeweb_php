<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
define('CHK_UPDATE_MODULES', 1);
define('CHK_UPDATE_CORE', 2);
class UpdateCheck {
	private $db;
	private $mh;
	private $ch;
	private $vh;
	
	public function __construct(IVersionHandler $versionHandler,
								IModuleHandler $moduleHandler,
								ICoreHandler $coreHandler,
								ISystemUpdateDb $updateStore) {
		$this->vh = $versionHandler;
		$this->mh = $moduleHandler;
		$this->ch = $coreHandler;
		$this->db = $updateStore;
	}

	public function check($type, $force = false) {
		if(!$force && !$this->timeout()){
			return;
		}

		if($type & CHK_UPDATE_CORE) $this->sortCoreUpdate();
		if($type & CHK_UPDATE_MODULES) $this->sortModuleUpdates();
	}
	
	private function timeout() {
		$t = $this->db->lastTimestamp();
		if(!$t){ $this->db->resetTimestamp(); }
		
		$c = time();
		$diff = $c - $t; 
		if($diff < 5400){ return false; }

		$this->db->resetTimestamp();
		return true;
	}
	
	private function sortModuleUpdates() {
				
		foreach(array('nws' => 'network', 'gws' => 'gateway') as $prefix => $type) {
			$services = array();
		
			foreach($this->mh->getInfoList($type) as $info) {

				$response = $this->vh->info("{$prefix}.{$info['module']}");
				if(!$response){ continue; }
				$check = $this->vh->needsUpdate($info['version'], $response['version']);

				if(!$check) {
					continue;
				}
				
				$services[$info['module']] = $response;
			}
			
			$this->db->delete($prefix);
			$this->db->add($prefix, $services);
			
		}
		
		
		
	}
	
	private function sortCoreUpdate() {
		$info = $this->ch->getInfo();
		$response = $this->vh->info("php.web.core");
		if(!$response) return;
		
		if(!$this->vh->needsUpdate($info['version'], $response['version'])) return;
		
		$this->db->delete('core');
		$this->db->add('core', array('php.web.core' => $response));
	}
	
	public function getUpdateQueue() {
		$queue = array();
		if( ($q = $this->db->services('nws')) ) {
			$queue['nws'] = $q;
		} else {
			$queue['nws'] = array();
		}
		if( ($q = $this->db->services('gws')) ) {
			$queue['gws'] = $q;
		} else {
			$queue['gws'] = array();
		}
		
		return $queue;
	}
}
