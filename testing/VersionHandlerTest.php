<?php
include '../autoload.php';

class VersionHandlerTest extends PHPUnit_Framework_TestCase {
	private $vh;
	public function setUp() {
		$this->vh = new VersionHandler();
	}

	public function testGetInfoPass() {
		$info = $this->vh->info('pkg.test');
		$this->assertEquals('1.0.0', $info['version']);
		$this->assertEquals('16-06-27', $info['date']);
		$this->assertEquals('8843d7f92416211de9ebb963ff4ce28125932878', $info['sha1']);
	}
	
	public function testGetInfoFail() {
		$info = $this->vh->info('pkg.void');
		$this->assertEquals(null, $info);
	}

	public function testNeedsUpdatePass() {
		$this->assertTrue($this->vh->needsUpdate('0.9.0', '1.0.0'));
	}
	
	public function testNeedsUpdateFail() {
		$this->assertFalse($this->vh->needsUpdate('1.0.0', '1.0.0'));
		$this->assertFalse($this->vh->needsUpdate('1.1.0', '1.0.0'));
	}
}