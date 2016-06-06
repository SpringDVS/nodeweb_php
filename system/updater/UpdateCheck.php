<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
define('CHK_UPDATE_MODULES', 1);
define('CHK_UPDATE_CORE', 2);

/**
 * Object used to perform and provide update check and information
 * 
 * This object will store any information about updates in the 
 * system update store. It can be used to retrieve that information
 * as well.
 */
class UpdateCheck {
	private $db;
	private $mh;
	private $ch;
	private $vh;

	/**
	 * Constructor assigns dependencies on interfaces
	 * 
	 * 
	 * @param IVersionHandler $versionHandler
	 * @param IModuleHandler $moduleHandler
	 * @param ICoreHandler $coreHandler
	 * @param ISystemUpdateDb $updateStore
	 */
	public function __construct(IVersionHandler $versionHandler,
								IModuleHandler $moduleHandler,
								ICoreHandler $coreHandler,
								ISystemUpdateDb $updateStore) {
		$this->vh = $versionHandler;
		$this->mh = $moduleHandler;
		$this->ch = $coreHandler;
		$this->db = $updateStore;
	}

	/**
	 * Check for any parts of the system that need updates
	 *  
	 * @param bitfield $type The type of check (CHK_UPDATE_MODULES|CHK_UPDATE_CORE)
	 * @param boolean $force Force the check instead of waiting for timeout 
	 */
	public function check($type, $force = false) {
		if(!$force && !$this->timeout()){
			return;
		}

		if($type & CHK_UPDATE_CORE) $this->sortCoreUpdate();
		if($type & CHK_UPDATE_MODULES) $this->sortModuleUpdates();
	}
	
	/**
	 * Check if the timeout has been reached since last check
	 * 
	 * @return true if reached timeout; otherwise false
	 */
	private function timeout() {
		$t = $this->db->lastTimestamp();
		if(!$t){ $this->db->resetTimestamp(); }
		
		$c = time();
		$diff = $c - $t; 
		if($diff < 5400){ return false; }

		$this->db->resetTimestamp();
		return true;
	}
	
	/**
	 * Check and accumulate the various modules that need updating
	 * 
	 * This method also stores the update information in the system
	 * update database
	 */
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
	
	/**
	 * Check and store any updates that are needed for the core
	 */
	private function sortCoreUpdate() {
		$this->db->delete('core');
		$info = $this->ch->getInfo();
		$response = $this->vh->info("php.web.core");
		if(!$response) return;
		
		if(!$this->vh->needsUpdate($info['version'], $response['version'])) return;
		
		$this->db->delete('core');
		$this->db->add('core', array('php.web.core' => $response));
	}
	
	/**
	 * Retrieve updates that have been queued
	 * 
	 * The updates queue comes in the form:
	 * 
	 *   array(
	 *     'nws' => array(), // Network service updates
	 *     'gws' => array(), // Gateway service updates
	 *     'core' => array() // Core updates
	 *   )
	 *
	 * Empty arrays if there are no updates waiting
	 * 
	 * @return array of updates
	 *   
	 */
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
		if( ($q = $this->db->services('core')) ) {
			$queue['core'] = $q;
		} else {
			$queue['core'] = array();
		}		
		return $queue;
	}
}
