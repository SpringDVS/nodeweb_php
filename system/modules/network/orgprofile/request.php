<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
use Flintstone\Flintstone;
if(!defined('SPRING_IF')) return [];
$options['dir'] = SpringDvs\Config::$sys['store_live'];
$db = new Flintstone('netservice_orgprofile', $options);
$profile = $db->get('profile');

return $profile;