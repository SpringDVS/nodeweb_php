<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

class User {
	private $id;
	public function __construct() {
		$this->id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
	}
	
	public function id() {
		return $this->id;
	}
}