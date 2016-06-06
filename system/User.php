<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

/**
 * Check if a user's login password is correct
 * @return 0 on no password provided; 1 on incorrect password; 2 on correct password
 */
function check_login() {
	$check = filter_input(INPUT_POST, 'form-password');
	if($check == null) {
		return 0; // Nothing
	} else if($check != \SpringDvs\Config::$spec['password']) {
		return 1; // Failed
	} 
	
	$_SESSION['admin'] = true;
	return 2; // Passed
}