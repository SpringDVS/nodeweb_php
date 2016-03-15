<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set("allow_url_fopen", true);
ini_set("allow_url_include", true);
error_reporting(E_ALL);

require 'vendor/autoload.php';


Flight::set('flight.views.path', 'system/views');
include 'system/routes.php';
include 'system/config.php';

Flight::start();

