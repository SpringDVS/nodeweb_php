<?php
$Gcm__ = array(
	'GatewayApiController'=>'controllers/GatewayApiController.php',
	'ManagementApiController'=>'controllers/ManagementApiController.php',
	'SpringApiController'=>'controllers/SpringApiController.php',
	'CoreHandler'=>'handlers/CoreHandler.php',
	'GatewayHandler'=>'handlers/GatewayHandler.php',
	'ModuleHandler'=>'handlers/ModuleHandler.php',
	'ProtocolHandler'=>'handlers/ProtocolHandler.php',
	'RequestHandler'=>'handlers/RequestHandler.php',
	'SemanticVersion'=>'handlers/SemanticVersion.php',
	'VersionHandler'=>'handlers/VersionHandler.php',
	'ICoreHandler'=>'interfaces/ICoreHandler.php',
	'IModuleHandler'=>'interfaces/IModuleHandler.php',
	'ISystemUpdateDb'=>'interfaces/ISystemUpdateDb.php',
	'IVersionHandler'=>'interfaces/IVersionHandler.php',
	'NetspaceKvs'=>'models/NetspaceKvs.php',
	'Resolution'=>'models/Resolution.php',
	'SystemUpdateKvs'=>'models/SystemUpdateKvs.php',
	'UpdateCheck'=>'updater/UpdateCheck.php',
	'UpdateRunner'=>'updater/UpdateRunner.php',
);
spl_autoload_register(function ($class) { 
	global $Gcm__;
	if(!isset($Gcm__[$class])) return; 
	include __DIR__.'/system/' . $Gcm__[$class];
});
