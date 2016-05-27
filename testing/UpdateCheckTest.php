<?php
include '../autoload.php';

class UpdateCheckTest extends PHPUnit_Framework_TestCase {
	private $mhmock;
	private $vhmock;
	private $sdmock;

	public function setUp() {
		
		$this->mhmock = $this->getMockBuilder('IModuleHandler')->getMock();
		$this->vhmock = $this->getMockBuilder('IVersionHandler')->getMock();
		$this->sdmock = $this->getMockBuilder('ISystemUpdateDb')->getMock();
	}
	
	private function setupNeedsUpdate() {
		$this->vhmock->method('needsUpdate')
		->will(
				$this->returnValueMap(
						array(
								array('1.1.0', '1.1.0', false), // Not out of date
								array('1.0.0', '1.1.0', true) // needs update
						)
						)
				);		
	}

	public function testUpdateModuleBehaviorNoTimeout() {
		$this->sdmock->method('lastTimestamp')
						->willReturn(time());
		
		$this->mhmock->expects($this->never())
						->method('getListInfo');

		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->sdmock);
		$check->checkModules();
	}
	
	public function testUpdateModuleBehaviorNoModules() {
		$this->mhmock->method('getInfoList')
						->willReturn(array());
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->sdmock);

		// Force it
		$check->checkModules(true);
	}
	
	public function testUpdateModuleBehaviorNoModulesAtTimeout() {
		$this->sdmock->method('lastTimestamp')
						->willReturn(time()-6000);

		$this->sdmock->expects($this->once())
						->method('resetTimestamp');
						
						
		$this->mhmock->method('getInfoList')
						->willReturn(array());

	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->sdmock);
	

		$check->checkModules();
	}
	
	public function testUpdateModuleBehaviorNoUpdate() {
	
		$this->mhmock->method('getInfoList')
						->will(
							$this->returnValueMap(
								array(		
									array('network', array( array('module' => 'testpkg','version' => '1.0.0') ) ),
									array('gateway', array())
								)
							)
						);
						
		$this->vhmock->expects($this->once())->method('info')->willReturn(array('version' => '1.0.0'));
		$this->vhmock->expects($this->once())->method('needsUpdate')->willReturn(false);
	
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->sdmock);
	
	
		$check->checkModules(true);
	}
	
	public function testUpdateModuleBehaviourOneUpdate() {
		
		$mockPackages =  array(array('module' => 'testpkg','version' => '1.0.0'));
		$mockVersion =  array('module' => 'testpkg','version' => '1.1.0');
		
		$this->mhmock->method('getInfoList')
		->will(
				$this->returnValueMap(
						array(
								array('network', $mockPackages),
								array('gateway', array())
							)
						)
				);
		
		$this->vhmock->expects($this->once())->method('info')->willReturn($mockVersion);
		$this->vhmock->expects($this->once())->method('needsUpdate')->willReturn(true);
		
		
		$this->sdmock->expects($this->at(1))
						->method('add')
						->with( $this->equalTo('nws'), $this->equalTo(array('testpkg' => $mockVersion )) );
		
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->sdmock);
			
		$check->checkModules(true);
	}
	
	public function testUpdateModuleBehaviourMultiModuleOneUpdate() {
	
		$mockPackages =  array(
							array('module' => 'testpkg','version' => '1.1.0'),
							array('module' => 'testpkg2','version' => '1.0.0'),
							array('module' => 'testpkg3','version' => '1.1.0')
						);
		
		$mockVersion =  array('module' => 'testpkg','version' => '1.1.0');
	
		$this->mhmock->method('getInfoList')
							->will(
								$this->returnValueMap(
									array(
										array('network', $mockPackages),
										array('gateway', array())
									)
								)
							);
	
		$this->vhmock->method('info')->willReturn($mockVersion);
		
		$this->setupNeedsUpdate();
	
		$this->sdmock->expects($this->at(1))
							->method('add')
							->with( 
								$this->equalTo('nws'),
								$this->equalTo(array('testpkg2' => $mockVersion )) 
							);
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->sdmock);
		$check->checkModules(true);
	}
	
	public function testUpdateModuleBehaviourMultiModuleMultiUpdate() {
	
		$mockPackages =  array(
				array('module' => 'testpkg','version' => '1.0.0'),
				array('module' => 'testpkg2','version' => '1.1.0'),
				array('module' => 'testpkg3','version' => '1.0.0')
		);
	
		$mockVersion =  array('module' => 'testpkg','version' => '1.1.0');
	
		$this->mhmock->method('getInfoList')
		->will(
				$this->returnValueMap(
						array(
								array('network', $mockPackages),
								array('gateway', array())
						)
						)
				);
	
		$this->vhmock->method('info')->willReturn($mockVersion);
	
		$this->setupNeedsUpdate();
	
		$this->sdmock->expects($this->at(1))
		->method('add')
		->with(
				$this->equalTo('nws'),
				$this->equalTo(array('testpkg' => $mockVersion, 'testpkg3' => $mockVersion ))
				);
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->sdmock);
		$check->checkModules(true);
	}
}