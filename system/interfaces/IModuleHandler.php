<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Interface for module handler objects
 *
 * Provides an interface for working with the modules
 */
interface IModuleHandler {
	
	/**
	 * Get an array of all the module infos of a particular type
	 *
	 * @param $type The string of type (network|gateway)
	 * @return array of module infos; otherwise an empty array
	 */
	public function getInfoList($type);
	
	/**
	 * Check if module of type is in a valid state
	 * @param $type String of module type (network|gateway)
	 * @param $module String name of module
	 * @return true if valid; otherwise false
	 */
	public function isValid($type, $module);
}
