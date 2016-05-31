<?php
include '../vendor/autoload.php';
include '../system/config.php';
include '../system/models/NetspaceKvs.php';
\SpringDvs\Config::$spec['testing'] = true;

class NetspaceKvsTest extends PHPUnit_Framework_TestCase {
	private function netspace() {
		
		$db = new NetspaceKvs(true, null);
		$this->reset($db->dbGsn());
		$this->reset($db->dbGtn());
		return $db;
	}
	
	private function reset(&$db) {
		try {
			unlink($db->getDatabase()->getPath());
		} catch(Exception $e) { }
	}
	
	public function testNetspaceKvsRegister() {
		$store = $this->netspace();
		$node = new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org);
		
		$store->gsnNodeRegister($node);
		
		$kvs = $store->dbGsn()->get('spring');
		
		$this->assertTrue(is_array($kvs));
		$this->assertEquals('host', $kvs['hostname']);
		$this->assertEquals('127.0.1.2', $kvs['address']);
		$this->assertEquals(SpringDvs\DvspService::http, $kvs['service']);
		$this->assertEquals(SpringDvs\DvspNodeState::disabled, $kvs['status']);
		$this->assertEquals(SpringDvs\DvspNodeType::org, $kvs['types']);
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsRegisterWithResource() {
		$store = $this->netspace();
		$node = new SpringDvs\Node("spring", "host/res/", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::disabled, 
									SpringDvs\DvspNodeType::org);
		
		$store->gsnNodeRegister($node);
		
		$kvs = $store->dbGsn()->get('spring');
		
		$this->assertTrue(is_array($kvs));
		$this->assertEquals('host/res/', $kvs['hostname']);
		$this->assertEquals('127.0.1.2', $kvs['address']);
		$this->assertEquals(SpringDvs\DvspService::http, $kvs['service']);
		$this->assertEquals(SpringDvs\DvspNodeState::disabled, $kvs['status']);
		$this->assertEquals(SpringDvs\DvspNodeType::org, $kvs['types']);
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsRegisterFail() {
		$store = $this->netspace();
		$node = new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::disabled, 
									SpringDvs\DvspNodeType::org);
		
		$store->gsnNodeRegister($node);
		
		$this->assertFalse($store->gsnNodeRegister($node));
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsUnregisterPass() {
		$store = $this->netspace();
		$node = SpringDvs\Node::from_nodestring("spring,host,127.0.1.2");
		$store->gsnNodeRegister($node);
		
		$this->assertTrue(is_array($store->dbGsn()->get('spring')));
		
		$store->gsnNodeUnregister($node);
		$this->assertFalse($store->dbGsn()->get('spring'));
		$this->reset($store->dbGsn());
	}

	public function testNetspaceKvsUnregisterFail() {
		$store = $this->netspace();
		$node = SpringDvs\Node::from_nodestring("spring2,host2,127.0.1.3");
		$store->gsnNodeRegister(SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"));
			
		$this->assertFalse($store->gsnNodeUnregister($node));
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodeBySpringnamePass() {
		$store = $this->netspace();
		$store->gsnNodeRegister(SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"));
		
		$node = $store->gsnNodeBySpringName('spring');
		$this->assertFalse($node === false);
		$this->assertEquals('spring', $node->springname());
		$this->assertEquals('host', $node->hostname());
		$this->assertEquals(array(127,0,1,2), $node->address());
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodeBySpringnameFail() {
		$store = $this->netspace();
		$store->gsnNodeRegister(SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"));
		
		$node = $store->gsnNodeBySpringName('void');
		$this->assertFalse($node);
		$this->reset($store->dbGsn());
		
	}

	public function testNetspaceKvsNodeByHostnamePass() {
		$store = $this->netspace();
		$store->gsnNodeRegister(SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"));
		
		$node = $store->gsnNodeByHostname('host');
		$this->assertFalse($node === false);
		$this->assertEquals('spring', $node->springname());
		$this->assertEquals('host', $node->hostname());
		$this->assertEquals(array(127,0,1,2), $node->address());
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodeByHostnameFail() {
		$store = $this->netspace();
		$store->gsnNodeRegister(SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"));
		
		$node = $store->gsnNodeByHostname('void');
		$this->assertFalse($node);
		$this->reset($store->dbGsn());
	}

	public function testNetsapceKvsNodeUpdatePass() {
		$store = $this->netspace();
		$node = SpringDvs\Node::from_nodestring("spring,host,127.0.1.2");
		
		$store->gsnNodeRegister($node);
		$this->assertEquals(SpringDvs\DvspNodeState::disabled, 
							$store->gsnNodeBySpringName("spring")->state());
		
		$node->updateState(SpringDvs\DvspNodeState::enabled);
		$store->gsnNodeUpdate($node);

		$this->assertEquals(SpringDvs\DvspNodeState::enabled, 
							$store->gsnNodeBySpringName("spring")->state());
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodeByStatePass() {
		$store = $this->netspace();
		$nodeA = new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org);
		
		$store->gsnNodeRegister($nodeA);
		$store->gsnNodeUpdate($nodeA);
		

		$nodeB = new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org);
		$store->gsnNodeRegister($nodeB);
		$store->gsnNodeUpdate($nodeB);
		
		$nodeC = new SpringDvs\Node("spring3", "host3", [127,0,1,4], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::unresponsive, 
									SpringDvs\DvspNodeType::org);
		$store->gsnNodeRegister($nodeC);
		$store->gsnNodeUpdate($nodeC);	
		
		$nodes = $store->gsnNodesByState(SpringDvs\DvspNodeState::enabled);
		$this->assertEquals(2, count($nodes));

		$this->assertEquals('spring', $nodes[0]->springname());
		$this->assertEquals('host', $nodes[0]->hostname());
		$this->assertEquals(array(127,0,1,2), $nodes[0]->address());

		$this->assertEquals('spring2', $nodes[1]->springname());
		$this->assertEquals('host2', $nodes[1]->hostname());
		$this->assertEquals(array(127,0,1,3), $nodes[1]->address());

		$unnodes = $store->gsnNodesByState(SpringDvs\DvspNodeState::unresponsive);
		
		$this->assertEquals(1, count($unnodes));

		$this->assertEquals('spring3', $unnodes[0]->springname());
		$this->assertEquals('host3', $unnodes[0]->hostname());
		$this->assertEquals(array(127,0,1,4), $unnodes[0]->address());
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodeByStateFail() {
		$store = $this->netspace();
		
		$nodeA = new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org);
		
		$store->gsnNodeRegister($nodeA);
		$store->gsnNodeUpdate($nodeA);
		

		$nodeB = new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org);
		$store->gsnNodeRegister($nodeB);
		$store->gsnNodeUpdate($nodeB);
		
		$nodeC = new SpringDvs\Node("spring3", "host3", [127,0,1,4], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::unresponsive, 
									SpringDvs\DvspNodeType::org);
		$store->gsnNodeRegister($nodeC);
		$store->gsnNodeUpdate($nodeC);	
		
		$nodes = $store->gsnNodesByState(SpringDvs\DvspNodeState::disabled);
		$this->assertTrue(empty($nodes));
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodesByTypesPass() {
			
		$store = $this->netspace();
		$store->gsnNodeRegister(new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));

		$store->gsnNodeRegister(new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));
		
		$store->gsnNodeRegister(new SpringDvs\Node("spring3", "host3", [127,0,1,4], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::unresponsive, 
									SpringDvs\DvspNodeType::root));
		
		$nodes = $store->gsnNodesByType(SpringDvs\DvspNodeType::org);
		$this->assertEquals(2, count($nodes));

		$this->assertEquals('spring', $nodes[0]->springname());
		$this->assertEquals('host', $nodes[0]->hostname());
		$this->assertEquals([127,0,1,2], $nodes[0]->address());

		$this->assertEquals('spring2', $nodes[1]->springname());
		$this->assertEquals('host2', $nodes[1]->hostname());
		$this->assertEquals(array(127,0,1,3), $nodes[1]->address());

		$unnodes = $store->gsnNodesByType(SpringDvs\DvspNodeType::root);
		
		$this->assertEquals(1, count($unnodes));

		$this->assertEquals('spring3', $unnodes[0]->springname());
		$this->assertEquals('host3', $unnodes[0]->hostname());
		$this->assertEquals(array(127,0,1,4), $unnodes[0]->address());
		$this->reset($store->dbGsn());		
	}
	
	public function testNetspaceKvsNodesByTypesFail() {
			
		$store = $this->netspace();
		$store->gsnNodeRegister(new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));

		$store->gsnNodeRegister(new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));
		
		$store->gsnNodeRegister(new SpringDvs\Node("spring3", "host3", [127,0,1,4], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::unresponsive, 
									SpringDvs\DvspNodeType::root));
		$nodes = $store->gsnNodesByType(SpringDvs\DvspNodeType::undefined);	
		$this->assertEquals(0, count($nodes));
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodesByAddressArrayPass() {
			
		$store = $this->netspace();

		$store->gsnNodeRegister(new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));
		
		$store->gsnNodeRegister(new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));

		$node = $store->gsnNodesByAddress([127,0,1,2]);
		
		$this->assertFalse($node === false);
		$this->assertEquals('spring', $node->springname());
		
		$this->reset($store->dbGsn());
	}

	public function testNetspaceKvsNodesByAddressStringPass() {
			
		$store = $this->netspace();
		$store->gsnNodeRegister(new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));
		
		$store->gsnNodeRegister(new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));

		$node = $store->gsnNodesByAddress("127.0.1.3");
		
		$this->assertFalse($node === false);
		$this->assertEquals('spring2', $node->springname());
		
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodesByAddressArrayFail() {
			
		$store = $this->netspace();

		$store->gsnNodeRegister(new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));
		
		$store->gsnNodeRegister(new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));

		$node = $store->gsnNodesByAddress([127,0,1,4]);
		
		$this->assertFalse($node);
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodesByAddressStringFail() {
			
		$store = $this->netspace();

		$store->gsnNodeRegister(new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));
		
		$store->gsnNodeRegister(new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));

		$node = $store->gsnNodesByAddress("127.0.1.4");
		$this->assertFalse($node);
		$node = $store->gsnNodesByAddress("127.0.1");
		$this->assertFalse($node);
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsNodesPass() {
		$store = $this->netspace();

		$store->gsnNodeRegister(new SpringDvs\Node("spring", "host", [127,0,1,2], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));
		
		$store->gsnNodeRegister(new SpringDvs\Node("spring2", "host2", [127,0,1,3], 
									SpringDvs\DvspService::http, 
									SpringDvs\DvspNodeState::enabled, 
									SpringDvs\DvspNodeType::org));

		$nodes = $store->gsnNodes();
		$this->assertEquals(2, count($nodes));

		$this->assertEquals('spring', $nodes[0]->springname());
		$this->assertEquals('host', $nodes[0]->hostname());
		$this->assertEquals(array(127,0,1,2), $nodes[0]->address());

		$this->assertEquals('spring2', $nodes[1]->springname());
		$this->assertEquals('host2', $nodes[1]->hostname());
		$this->assertEquals(array(127,0,1,3), $nodes[1]->address());
		$this->reset($store->dbGsn());
	}
	
	public function testNetspaceKvsGtnNodeRegisterPass() {
		$store = $this->netspace();

		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		$v = $store->dbGtn()->get('spring__esusx');
		$this->assertTrue(is_array($v));
		
		$this->assertEquals('host', $v['hostname']);
		$this->assertEquals('127.0.1.2', $v['address']);
		$this->assertEquals('esusx', $v['geosub']);
		$this->reset($store->dbGtn());
	}
	
	public function testNetspaceKvsGtnNodeRegisterFail() {
		$store = $this->netspace();

		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);

		$v = $store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		$this->assertFalse($v);
	}

	public function testNetspaceKvsGtnNodeUnregisterPass() {
		$store = $this->netspace();

		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		
		$this->assertTrue(is_array($store->dbGtn()->get('spring__esusx')));
		
		$store->gtnGeosubUnregister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		$this->assertFalse($store->dbGtn()->get('spring__esusx'));
	}
	
	public function testNetspaceKvsGtnNodeUnregisterFail() {
		$store = $this->netspace();

		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		
		$this->assertTrue(is_array($store->dbGtn()->get('spring__esusx')));
		
		$v = $store->gtnGeosubUnregister(
				SpringDvs\Node::from_nodestring("void,host,127.0.1.2"), 
				'esusx'
			);
		$this->assertFalse($v);
	}

	public function testNetspaceKvsGtnNodeFromSpringnamePass() {
		$store = $this->netspace();

		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		
		$node = $store->gtnGeosubNodeBySpringname('spring', 'esusx');
		
		$this->assertFalse($node === false);
		
		$this->reset($store->dbGtn());
	}

	public function testNetspaceKvsGtnNodeFromSpringnameFail() {
		$store = $this->netspace();

		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		
		$node = $store->gtnGeosubNodeBySpringname('void', 'esusx');
		
		$this->assertFalse($node);
		
		$this->reset($store->dbGtn());
	}
	
	public function testNetspaceKvsGtnGeosubRootNodesPass() {
		$store = $this->netspace();

		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring2,host2,127.0.1.3"), 
				'esusx'
			);
		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring3,host3,127.0.1.4"), 
				'wsusx'
			);
		
		$nodes = $store->gtnGeosubRootNodes('esusx');
		
		$this->assertEquals(2, count($nodes));

		$this->assertEquals('spring', $nodes[0]->springname());
		$this->assertEquals('host', $nodes[0]->hostname());
		$this->assertEquals([127,0,1,2], $nodes[0]->address());
		
		$this->assertEquals('spring2', $nodes[1]->springname());
		$this->assertEquals('host2', $nodes[1]->hostname());		
		$this->assertEquals([127,0,1,3], $nodes[1]->address());
		
		$nodesB = $store->gtnGeosubRootNodes('wsusx');
		$this->assertEquals(1, count($nodesB));

		$this->assertEquals('spring3', $nodesB[0]->springname());
		$this->assertEquals('host3', $nodesB[0]->hostname());
		$this->assertEquals([127, 0, 1, 4], $nodesB[0]->address());
		$this->reset($store->dbGtn());
	}
	
	public function testNetspaceKvsGtnGeosubRootNodesFail() {
		$store = $this->netspace();

		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring,host,127.0.1.2"), 
				'esusx'
			);
		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring2,host2,127.0.1.3"), 
				'esusx'
			);
		$store->gtnGeosubRegister(
				SpringDvs\Node::from_nodestring("spring3,host3,127.0.1.4"), 
				'wsusx'
			);

		$nodes = $store->gtnGeosubRootNodes('surry');
		$this->assertEquals(0, count($nodes));
		$this->reset($store->dbGtn());
	}
	
	public function testNetspaceKvsLiveEnvUpdateAddress() {
		$store = $this->netspace();

		$node = SpringDvs\Node::from_nodestring("spring,host,127.0.1.2");
		$store->gsnNodeRegister($node);

		$c1 = $store->gsnNodeBySpringName("spring");
		$this->assertEquals([127,0,1,2], $c1->address());
		
		$this->assertEquals(
				true,
				update_address_live_env($store, "spring,host,192.168.55.66")
		);
		$c2 = $store->gsnNodeBySpringName("spring");
		$this->assertEquals([192,168,55,66], $c2->address());
	}

	public function testNetspaceKvsLiveEnvAddRoot() {
		$store = $this->netspace();

		$this->assertEquals(
			true,
			add_geosub_root_live_env($store, "spring,host,192.168.1.2,esusx")
		);
		
		$c1 = $store->gtnGeosubNodeBySpringname("spring", "esusx");
		
		$this->assertEquals("spring", $c1->springname());
		$this->assertEquals("host", $c1->hostname());
		$this->assertEquals([192,168,1,2], $c1->address());
		
	}
}