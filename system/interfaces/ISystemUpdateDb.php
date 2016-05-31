<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

interface ISystemUpdateDb {
	public function add($prefix, $services);
	public function services($prefix);
	public function delete($prefix);
	
	public function lastTimestamp();
	public function resetTimestamp();
}