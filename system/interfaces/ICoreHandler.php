<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Interface for core handler object
 */
interface ICoreHandler {
	/**
	 * Get current version informatin about the core
	 * @return version array
	 */
	public function getInfo();
}
