<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

use Flintstone\Flintstone;
use SpringDvs;
use SpringDvs\Node;
use SpringDvs\DvspNodeState;
use SpringDvs;

/**
 * Provides Key-Value Store implementation of the netspace model
 * 
 * The netspace model is the point that handles the state of the netspace.
 * Currently it has pretty minimal use but will be utilised more when GSN
 * caching is working, more so when functional-peer-elevation is online.
 * 
 * Current implementation uses Flintstone as the store system
 */
class NetspaceKvs implements SpringDvs\iNetspace {
	private $geosubNetspace;
	private $geotopNetspace;
	
	/**
	 * Constructor initialises Flintstone
	 * 
	 * The configuration options for test and live stores are used
	 * as the paths given to Flinstone
	 * 
	 * @param boolean $unitTest true if unit testing;otherwise false if live
	 */
	public function __construct($unitTest = false) {
		$options = array();
		if( $unitTest ) {
			$options['dir'] = SpringDvs\Config::$sys['store_test'];
		} else {
			$options['dir'] = SpringDvs\Config::$sys['store_live'];
		}

		$this->geosubNetspace = new Flintstone('node_geosub', $options);
		$this->geotopNetspace = new Flintstone('node_geotop', $options);
	}

	/**
	 * Get node by IP Address
	 * 
	 * @param string $address IP Address
	 * @return SpringDvs\Node on success; otherwise false if not found
	 */
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

	/**
	 * Get node by hostname
	 * @param string $hostname 
	 * @return SpringDvs\Node on success; otherwise false if not found
	 */
	public function gsnNodeByHostname($hostname) {
		foreach($this->geosubNetspace->getKeys() as $s) {
			$v = $this->geosubNetspace->get($s);
			if($v['hostname'] == $hostname) {
				return $this->constructNode ($s, $v);
			}
		}
		
		return false;
	}

	/**
	 * Get node by Springname
	 * 
	 * @param string $springname Springname of node
	 * @return SpringDvs\Node on success; otherwise false if not found
	 */
	public function gsnNodeBySpringName($springname) {
		if( ($n = $this->geosubNetspace->get($springname)) )
			return $this->constructNode($springname, $n);
		else
			return false;
	}

	/**
	 * Get all nodes of type
	 * 
	 * @param bitfield $types A bitfield of types to check against
	 * @return Array of SpringDvs\Node on success; otherwise empty array
	 */
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
	
	/**
	 * Get all nodes by state
	 *
	 * @param SpringDvs\DvspNodeState $state State to check against
	 * @return Array of SpringDvs\Node on success; otherwise empty array
	 */
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

	/**
	 * Get all nodes in the local GSN
	 *
	 * @return Array of SpringDvs\Node on success; otherwise empty array
	 */
	public function gsnNodes() {
		$list = array();
		foreach($this->geosubNetspace->getKeys() as $s) {
			$v = $this->geosubNetspace->get($s);
			$list[] = $this->constructNode($s, $v);
		}
		
		return $list;
	}

	/**
	 * Register a new node in the GSN
	 * 
	 * @param SpringDvs\Node $node The node to register
	 * @return true if registered; otherwise false
	 */
	public function gsnNodeRegister($node) {
		if($this->geosubNetspace->get($node->springname()) !== false
		|| $this->gsnNodeByHostname($node->hostname()) !== false)
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

	/**
	 * Unregister a new node in the GSN
	 * 
	 * @param SpringDvs\Node $node The node to unregister
	 * @return true if unregistered; otherwise false
	 */
	public function gsnNodeUnregister($node) {
		if($this->geosubNetspace->get($node->springname()) === false 
		|| $this->gsnNodeByHostname($node->hostname()) === false)
			return false;

		$this->geosubNetspace->delete($node->springname());
		return true;
	}

	/**
	 * Update status/state of node
	 *
	 * The status will be set to the information provided in
	 * the node object
	 * 
	 * @param SpringDvs\Node $node The node to update with update details
	 * @return true if updated; otherwise false
	 */
	public function gsnNodeUpdate($node) {
		$details = $this->geosubNetspace->get($node->springname());
		
		if($details === false) return false;
		$details['status'] = $node->state();
		
		$this->geosubNetspace->set($node->springname(), $details);
		return true;
	}

	/**
	 * Get root nodes (Not to be used yet)
	 */
	public function gtnRootNodes() {
		
	}
	
	/**
	 * Get list of GSNs (not to be used)
	 */
	public function gtnGeosubs() {
		
	}

	/**
	 * Register a new root node in the top network
	 *
	 * @param SpringDvs\Node $node The node to register as root
	 * @param string $geosub The name of the GSN of which the node is a root
	 * @return true if added; otherwise false
	 */	
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

	/**
	 * Unregister a new root node from the top network
	 *
	 * @param SpringDvs\Node $node The node to unregister as root
	 * @param string $geosub The name of the GSN of which the node was a root
	 * @return true if removed; otherwise false
	 */	
	public function gtnGeosubUnregister($node, $geosub) {

		$key = $node->springname() ."__".$geosub;

		if($this->geotopNetspace->get($key) === false)
			return false; 
		
		$this->geotopNetspace->delete($key);
		return true;
	}

	/**
	 * Get root nodes of a GSN
	 *
	 * @param string $geosub The name of the GSN
	 * @return array of \SpringDvs\Node objects; otherwise empty array 
	 */
	public function gtnGeosubRootNodes($geosub) {

		$list = array();

		foreach($this->geotopNetspace->getKeys() as $k) {
			$v = $this->geotopNetspace->get($k);
			if($v['geosub'] == $geosub)
				$list[] = $this->constructRootNode($k, $v);
		}

		return $list;
	}

	/**
	 * Get a root node of GSN by it's springname
	 *
	 * @param string $springname The springname of node
	 * @param string $geosub The name of the GSN of which the node is a root
	 * @return \SpringDvs\Node if found; otherwise false
	 */
	public function gtnGeosubNodeBySpringname($springname, $geosub) {
		$key = $springname ."__".$geosub;
		$v = $this->geotopNetspace->get($key);
		if($v === false)
			return false; 
		
		return $this->constructRootNode($springname, $v);		
	}
	
	/**
	 * Get a handle to the internal GSN database object
	 */
	public function &dbGsn() {
		return $this->geosubNetspace;
	}
	
	/**
	 * Get a handle to the internal GTN database object
	 */
	public function &dbGtn() {
		return $this->geotopNetspace;
	}
	
	/**
	 * Construct a node from springname and details from store
	 * 
	 * This is used to fill out a node from the information retrieved
	 * from the database
	 * 
	 * @param string $springname The springname of the node
	 * @param array $details Details of the node 
	 */
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
	
	/**
	 * Dodgy construction of Root node from top netspace
	 * 
	 * Please see ToDo in netlib
	 * 
	 * @param string $springname The springname of the node
	 * @param array $details Details of the node 
	 */
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


/**
 * Live testing environment functions
 */

function reset_live_env(NetspaceKvs $nio) {
	if(!\SpringDvs\Config::$spec['testing']) return;

	try {
			unlink($nio->dbGsn()->getDatabase()->getPath());
			unlink($nio->dbGtn()->getDatabase()->getPath());
	} catch(Exception $e) { }
	
	return true;
}

function update_address_live_env(NetspaceKvs $nio, $nodestr) {
	if(!\SpringDvs\Config::$spec['testing']) return false;
	
	$node = SpringDvs\Node::from_nodestring($nodestr);
	$current = $nio->dbGsn()->get($node->springname());
	if(!$current) return false;
	
	$current['address'] = \SpringDvs\Node::addressToString($node->address());
	$nio->dbGsn()->set($node->springname(), $current);
	return true;
}

function add_geosub_root_live_env(NetspaceKvs $nio, $nodestr) {
	if(!\SpringDvs\Config::$spec['testing']) return false;
	
	$node = \SpringDvs\Node::from_nodestring($nodestr);
	$node->updateService(\SpringDvs\DvspService::dvsp);
	$geosub = \SpringDvs\Node::geosubFromNodeRegister($nodestr);
	if(!$geosub) return false;
	
	$nio->gtnGeosubRegister($node, $geosub);
	return true;
}
