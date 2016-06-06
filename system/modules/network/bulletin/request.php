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



parse_str($url->query(), $queries);
$bulletins = array_reverse($bulletins);
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
	}

} else {
	foreach($bulletins as $key => &$value) {
		$tags = explode(',', $value['tags']);
		$value['tags'] = $tags;
	}
	$final = $bulletins;
}

if(isset($queries['limit']) && intval($queries['limit'])) {
	$final = array_slice($final, 0, $queries['limit']);
}

return array_values($final);