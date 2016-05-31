<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

interface IModuleHandler {
	public function getInfoList($type);
	public function isValid($type, $module);
	
	public function install($type, $module, $archive);
	public function upgrade($type, $module, $archive);
}
