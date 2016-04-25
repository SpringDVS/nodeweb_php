<?php
include '../vendor/autoload.php';

class DvspNodeTest extends PHPUnit_Framework_TestCase {
	public function testFromNodeString() {
		$node = SpringDvs\Node::from_nodestring("spring,host,127.0.1.2");
		
		$this->assertEquals( "spring", $node->springname() );
		$this->assertEquals( "host", $node->hostname() );
		$this->assertEquals( Array(127,0,1,2), $node->address() );
	}

	public function testFromSpringname() {
		$node = SpringDvs\Node::from_springname("spring");
		
		$this->assertEquals( "spring", $node->springname() );
	}
	
	public function testFromNodeStringWithResource() {
		$node = SpringDvs\Node::from_nodestring("spring,sub.host.tld/node/,127.0.1.2");
		
		$this->assertEquals( "spring", $node->springname() );
		$this->assertEquals( "sub.host.tld", $node->hostname() );
		$this->assertEquals( Array(127,0,1,2), $node->address() );
		$this->assertEquals( "node/", $node->resource() );
	}
	
	public function testNodeStringFormat() {
		$node = SpringDvs\Node::from_nodestring("spring,sub.host.tld,127.0.1.2");
		
		$this->assertEquals("spring,sub.host.tld,127.0.1.2", $node->toNodeString());
	}
	
	public function testNodeStringFormatWithResource() {
		$node = SpringDvs\Node::from_nodestring("spring,sub.host.tld/node/,127.0.1.2");
		
		$this->assertEquals("spring,sub.host.tld,127.0.1.2", $node->toNodeString());
	}
	
	public function testNodeRegisterFormat() {
		$node = SpringDvs\Node::from_nodestring("spring,sub.host.tld,127.0.1.2");
		
		$this->assertEquals("spring,sub.host.tld", $node->toNodeRegister());
	}
	
	public function testNodeRegisterWithResource() {
		$node = SpringDvs\Node::from_nodestring("spring,sub.host.tld/node/,127.0.1.2");
		
		$this->assertEquals("spring,sub.host.tld/node/", $node->toNodeRegister());
	}

	public function testGeosubFromNodeRegPass() {
		$gsn = \SpringDvs\Node::geosubFromNodeRegister("spring,sub.host.tld/node/,127.0.1.2,esusx");
		
		$this->assertEquals("esusx", $gsn);
	}
	
	public function testGeosubFromNodeRegFail() {
		$gsn = \SpringDvs\Node::geosubFromNodeRegister("spring,sub.host.tld/node/,127.0.1.2");
		
		$this->assertEquals(false, $gsn);
	}
}