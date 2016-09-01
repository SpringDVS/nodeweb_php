<?php
use Flintstone\Flintstone;

/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
if(!defined('SPRING_IF')) return [];

$options['dir'] = SpringDvs\Config::$sys['store_live'];
$keyring = new Flintstone('keyring', $options);
$queries = [];

parse_str($url->query(), $queries);

if(empty($queries)) {
	$keys = $keyring->get('this');
	if(!$keys || !isset($keys['public'])) {
		return array('certificate' => 'error');
	}
	
	$cert = $keys['public'];
	
	return array('certificate' => $cert);
}

if(isset($queries['keyonly'])) {
	$keys = $keyring->get('this');
	if(!$keys || !isset($keys['public'])) {
		return array('key' => 'error');
	}
	
	$cert = $keys['public'];
	
	return array('key' => $cert['armor']);
}