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
}