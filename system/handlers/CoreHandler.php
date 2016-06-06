<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Object provides access to core information
 * 
 * The core is everything but the modules, including all
 * the vendor dependencies.
 * 
 */
class CoreHandler implements ICoreHandler {
	
	/**
	 * Get version information about the core
	 * 
	 * @return info array if exists; otherwise null
	 */
	public function getInfo() {
		if(!file_exists(__DIR__.'/../coreinfo.php')) return;
		
		return include __DIR__.'/../coreinfo.php';
	}

}
