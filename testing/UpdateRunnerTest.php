<?php
include '../autoload.php';

class UpdateRunnerTest extends PHPUnit_Framework_TestCase {
	private $phmock;
	private $sdmock;
	
	public function setUp() {
		$this->phmock = $this->getMockBuilder('IPackageHandler')->getMock();
		$this->sdmock = $this->getMockBuilder('ISystemUpdateDb')->getMock();
	}
	
	public function testModuleNoRemotePackage() {
		$this->phmock->method('pull')->willReturn(null);
		$ur = new UpdateRunner($this->phmock);
		$this->assertEquals(UpdateRunner::FAIL_DOWNLOAD, $ur->serviceNetwork('test', array('version'=>'1.0.0','sha1'=>'foo')));
	}
	
	public function testModuleInvalidPackage() {
		$this->phmock->method('validate')->willReturn(false);
		$this->phmock->method('pull')->willReturn("/foo.tgz");
		
		$ur = new UpdateRunner($this->phmock);

		$this->assertEquals(UpdateRunner::FAIL_CHECKSUM, $ur->serviceNetwork('test', array('version'=>'1.0.0','sha1'=>'foo')));		
	}
	
	public function testModuleValidPackage() {
		try {
			mkdir('system/modules/network/test/',0777,true);
		} catch(Exception $e) { } 
		
		$this->phmock->method('validate')->willReturn(true);
		$this->phmock->method('pull')->willReturn("/nws.test_1.0.0.tgz");
		$this->phmock->method('unpack')->with( $this->equalTo('/nws.test_1.0.0.tgz'), $this->equalTo('system/modules/network/'))
						->willReturn(true);
		
		$ur = new UpdateRunner($this->phmock);
		
		$this->assertEquals(UpdateRunner::OK, $ur->serviceNetwork('test', array('version'=>'1.0.0','sha1'=>'foo')));		
		
		rmdir('system/modules/network/test');
		rmdir('system/modules/network');
		rmdir('system/modules');
		rmdir('system');
	}
	
	public function testCoreNoRemotePackage() {
		$this->phmock->method('pull')->willReturn(null);
		$ur = new UpdateRunner($this->phmock);
		$this->assertEquals(UpdateRunner::FAIL_DOWNLOAD, $ur->core(array('version'=>'1.0.0','sha1'=>'foo')));
	}
	
	public function testCoreInvalidPackage() {
		$this->phmock->method('validate')->willReturn(false);
		$this->phmock->method('pull')->willReturn("/foo.tgz");
		
		$ur = new UpdateRunner($this->phmock);

		$this->assertEquals(UpdateRunner::FAIL_CHECKSUM, $ur->core(array('version'=>'1.0.0','sha1'=>'foo')));		
	}
	
	public function testCoreValidPackage() {
		
		$this->phmock->method('validate')->willReturn(true);
		$this->phmock->method('pull')->willReturn("/nws.test_1.0.0.tgz");
		$this->phmock->method('unpack')->with( $this->equalTo('/nws.test_1.0.0.tgz'), $this->equalTo('./'))
						->willReturn(true);
		
		$ur = new UpdateRunner($this->phmock);
		
		$this->assertEquals(UpdateRunner::OK, $ur->core(array('version'=>'1.0.0','sha1'=>'foo')));		
		
	}
}