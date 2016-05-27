<?php

interface IModuleHandler {
	public function getInfoList($type);
	public function isValid($type, $module);
	
	public function install($type, $module, $archive);
	public function upgrade($type, $module, $archive);
}
