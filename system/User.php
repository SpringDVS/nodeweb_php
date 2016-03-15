<?php

class User {
	private $id;
	public function __construct() {
		$this->id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
	}
	
	public function id() {
		return $this->id;
	}
}