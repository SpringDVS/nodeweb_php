<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

use Flintstone\Flintstone;

class NetspaceKvs implements SpringDvs\iNetspace {
	private $geosubNetspace;
	private $geotopNetspace;
	
	public function __construct($unitTest = false, $testDir = "") {
		$options = array();
		if( $unitTest ) {
			$options['dir'] = $testDir;
		} else {
			// Actual init
		}
		$this->geosubNetspace = new Flintstone('gsn_testunit', $options);
		$this->geotopNetspace = new Flintstone('gtn_testunit', $options);
	}


	public function gsnNodesByAddress($address) {
		
		$addr = is_string($address) ? 
				$address : 
				SpringDvs\Node::addressToString($address);
		
				
		foreach($this->geosubNetspace->getKeys() as $s) {
			$v = $this->geosubNetspace->get($s);
			if($v['address'] == $addr) {
				return $this->constructNode ($s, $v);
			}
		}
		
		return false;		
	}

	public function gsnNodeByHostname($hostname) {
		foreach($this->geosubNetspace->getKeys() as $s) {
			$v = $this->geosubNetspace->get($s);
			if($v['hostname'] == $hostname) {
				return $this->constructNode ($s, $v);
			}
		}
		
		return false;
	}

	public function gsnNodeBySpringName($springname) {
		if( ($n = $this->geosubNetspace->get($springname)) )
			return $this->constructNode($springname, $n);
		else
			return false;
	}

	public function gsnNodesByType($types) {
		$list = array();
		foreach($this->geosubNetspace->getKeys() as $s) {
			$v = $this->geosubNetspace->get($s);
			if($v['types'] & $types) {
				$list[] = $this->constructNode ($s, $v);
			}
		}
		
		return $list;
	}

	public function gsnNodesByState($state) {
		$list = array();
		foreach($this->geosubNetspace->getKeys() as $s) {
			$v = $this->geosubNetspace->get($s);
			if($v['status'] == $state) {
				$list[] = $this->constructNode ($s, $v);
			}
		}
		
		return $list;
	}

	public function gsnNodes() {
		$list = array();
		foreach($this->geosubNetspace->getKeys() as $s) {
			$v = $this->geosubNetspace->get($s);
			$list[] = $this->constructNode($s, $v);
		}
		
		return $list;
	}

	public function gsnNodeRegister($node) {
		if($this->geosubNetspace->get($node->springname()) !== false)
			return false; 
 
		$nodeArray = array(
			'hostname' => $node->toHostResource(),
			'address' => SpringDvs\Node::addressToString($node->address()),
			'service' => $node->service(),
			'status' => SpringDvs\DvspNodeState::disabled,
			'types' => $node->types(),
		);
		
		$this->geosubNetspace->set($node->springname(), $nodeArray);
		return true;
	}

	public function gsnNodeUnregister($node) {
		if($this->geosubNetspace->get($node->springname()) === false)
			return false;

		$this->geosubNetspace->delete($node->springname());
		return true;
	}

	public function gsnNodeUpdate($node) {
		$details = $this->geosubNetspace->get($node->springname());
		
		if($details === false) return false;
		$details['status'] = $node->state();
		
		$this->geosubNetspace->set($node->springname(), $details);
		return true;
	}

	public function gtnRootNodes() {
		
	}

	public function gtnGeosubs() {
		
	}

	public function gtnGeosubRegister($node, $geosub) {
		$key = $node->springname() ."__".$geosub;
		if($this->geotopNetspace->get($key) !== false)
			return false; 
 
		$nodeArray = array(
			'hostname' => $node->toHostResource(),
			'address' => SpringDvs\Node::addressToString($node->address()),
			'service' => $node->service(),
			'priority' => SpringDvs\DvspNodeState::disabled,
			'geosub' => $geosub,
		);
		
		$this->geotopNetspace->set($key, $nodeArray);
		return true;
	}

	public function gtnGeosubUnregister($node, $geosub) {

		$key = $node->springname() ."__".$geosub;

		if($this->geotopNetspace->get($key) === false)
			return false; 
		
		$this->geotopNetspace->delete($key);
		return true;
	}

	public function gtnGeosubRootNodes($geosub) {

		$list = array();

		foreach($this->geotopNetspace->getKeys() as $k) {
			$v = $this->geotopNetspace->get($k);
			if($v['geosub'] == $geosub)
				$list[] = $this->constructRootNode($k, $v);
		}

		return $list;
	}

	public function gtnGeosubNodeBySpringname($springname, $geosub) {
		$key = $springname ."__".$geosub;
		$v = $this->geotopNetspace->get($key);
		if($v === false)
			return false; 
		
		return $this->constructRootNode($springname, $v);		
	}
	
	public function &dbGsn() {
		return $this->geosubNetspace;
	}
	
	public function &dbGtn() {
		return $this->geotopNetspace;
	}
	
	private function constructNode($springname, $details) {
		return new SpringDvs\Node(
				$springname, 
				$details['hostname'],
				SpringDvs\Node::addressFromString($details['address']),
				$details['service'],
				$details['status'],
				$details['types']
		);
	}
	
	private function constructRootNode($springname, $details) {
		$vals = explode("__", $springname);
		return new SpringDvs\Node(
				$vals[0], 
				$details['hostname'],
				SpringDvs\Node::addressFromString($details['address']),
				$details['service'],
				SpringDvs\DvspNodeState::unspecified,
				SpringDvs\DvspNodeType::undefined
		);
	}
}


function reset_live_env(NetspaceKvs $nio) {
	if(!\SpringDvs\Config::$spec['testing']) return;

	try {
			unlink($nio->dbGsn()->getDatabase()->getPath());
			unlink($nio->dbGtn()->getDatabase()->getPath());
	} catch(Exception $e) { }
}

function update_address_live_env(NetspaceKvs $nio, $nodestr) {
	if(!\SpringDvs\Config::$spec['testing']) return false;
	
	$node = SpringDvs\Node::from_nodestring($nodestr);
	$current = $nio->dbGsn()->get($node->springname());
	if(!$current) return false;
	
	$current['address'] = \SpringDvs\Node::addressToString($node->address());
	$nio->dbGsn()->set($node->springname(), $current);
}

function add_geosub_root_live_env(NetspaceKvs $nio, $nodestr) {
	if(!\SpringDvs\Config::$spec['testing']) return;
	
	$node = \SpringDvs\Node::from_nodestring($nodestr);
	$node->updateService(\SpringDvs\DvspService::dvsp);
	$geosub = \SpringDvs\Node::geosubFromNodeRegister($nodestr);
	if(!$geosub) return;
	
	$nio->gtnGeosubRegister($node, $geosub);
}