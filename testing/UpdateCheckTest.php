<?php
include '../autoload.php';

class UpdateCheckModuleTest extends PHPUnit_Framework_TestCase {
	private $mhmock;
	private $vhmock;
	private $chmock;
	private $sdmock;

	public function setUp() {
		$this->chmock = $this->getMockBuilder('ICoreHandler')->getMock();
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

// --- MODULE UPDATE ---

	public function testUpdateModuleBehaviorNoTimeout() {
		$this->sdmock->method('lastTimestamp')
						->willReturn(time());
		
		$this->mhmock->expects($this->never())
						->method('getListInfo');

		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
		$check->check(CHK_UPDATE_MODULES);
	}
	
	public function testUpdateModuleBehaviorNoModules() {
		$this->mhmock->method('getInfoList')
						->willReturn(array());
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);

		// Force it
		$check->check(CHK_UPDATE_MODULES, true);
	}
	
	public function testUpdateModuleBehaviorNoModulesAtTimeout() {
		$this->sdmock->method('lastTimestamp')
						->willReturn(time()-6000);

		$this->sdmock->expects($this->once())
						->method('resetTimestamp');
						
						
		$this->mhmock->method('getInfoList')
						->willReturn(array());

	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
	

		$check->check(CHK_UPDATE_MODULES);
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
	
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
	
	
		$check->check(CHK_UPDATE_MODULES, true);
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
		
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
			
		$check->check(CHK_UPDATE_MODULES, true);
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
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
		$check->check(CHK_UPDATE_MODULES, true);
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
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
		$check->check(CHK_UPDATE_MODULES, true);
	}
	
// --- CORE UPDATE ---

	public function testUpdateCoreBehaviorNoTimeout() {
		$this->sdmock->method('lastTimestamp')
		->willReturn(time());
	
		$this->mhmock->expects($this->never())
		->method('getListInfo');
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
		$check->check(CHK_UPDATE_CORE, true);
	}
	
	public function testUpdateCoreBehaviorNoUpdate() {
	
		$this->mhmock->method('getInfo')
		->willReturn(array('module' => 'testpkg','version' => '0.9.0'));
	
		$this->vhmock->expects($this->once())->method('info')->willReturn(array('version' => '0.9.0'));
		$this->vhmock->expects($this->once())->method('needsUpdate')->willReturn(false);
	
		//$this->sdmock->expects($this->never())->method('delete');
		$this->sdmock->expects($this->never())->method('add');
	
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
	
	
		$check->check(CHK_UPDATE_CORE, true);
	}

	public function testUpdateCoreBehaviourUpdate() {
		$this->mhmock->method('getInfo')
		->willReturn(array('module' => 'php.web.core','version' => '0.4.0'));
		
		$this->vhmock->expects($this->once())->method('info')->willReturn(array('version' => '0.9.0'));
		$this->vhmock->expects($this->once())->method('needsUpdate')->willReturn(true);
		
		//$this->sdmock->expects($this->once())->method('delete');
		$this->sdmock->expects($this->once())->method('add')->with(
																$this->equalTo('core'),
																$this->equalTo(array('php.web.core' => 
																					array('version' => '0.9.0')
																					
																					)
																				)
																			);
		
		$check = new UpdateCheck($this->vhmock, $this->mhmock, $this->chmock, $this->sdmock);
		
		
		$check->check(CHK_UPDATE_CORE, true);		
	}
}	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	