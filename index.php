<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set("allow_url_fopen", true);
ini_set("allow_url_include", true);
error_reporting(E_ALL);

require 'vendor/autoload.php';
require 'autoload.php';


Flight::set('flight.views.path', 'system/views');
include 'system/routes.php';
include 'system/config.php';


Flight::start();