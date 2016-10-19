<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
use Flintstone\Flintstone;
if(!defined('SPRING_IF')) return [];
$options['dir'] = SpringDvs\Config::$sys['store_live'];
$db = new Flintstone('netservice_bulletin', $options);

$bulletins = $db->getAll();
$final = [];
$queries = [];

if(isset($rpath[0]) && !empty($rpath[0])) {

	foreach($bulletins as &$value) {
		if(!isset($value['uid']) || $value['uid'] != $rpath[0]) continue;
		
		return $value;
	}
	return array();
}


parse_str($url->query(), $queries);
$bulletins = array_reverse($bulletins);

if(isset($queries['categories'])) {
	$qcats = explode(',', $queries['categories']);
	$filtered = array();
	foreach($bulletins as $key => &$value) {
		if(!isset($value['categories'])) {
			continue;
		}
		$cats = explode(',', $value['categories']);
		foreach($cats as $cat) {
			if(in_array(trim($cat), $qcats)) {
				$filtered[$key] = $value;
			}
		}
	}
	
	$bulletins = $filtered;
}

if(isset($queries['tags'])) {

	$qtags = explode(',', $queries['tags']);
	foreach($bulletins as $key => &$value) {
		$tags = explode(',', $value['tags']);
		foreach($tags as $tag) {
			if(in_array(trim($tag), $qtags)){ 
				$value['tags'] = $tags;
				$final[$key] = $value;
			}
		}
		unset($value['content']);
	}

} else {
	foreach($bulletins as $key => &$value) {
		$tags = explode(',', $value['tags']);
		$value['tags'] = $tags;
		unset($value['content']);
	}
	$final = $bulletins;
}

if(isset($queries['limit']) && intval($queries['limit'])) {
	$final = array_slice($final, 0, $queries['limit']);
}

return array_values($final);