
<?php
	\SpringDvs\Config::$spec['springname'] = "cci";
	\SpringDvs\Config::$spec['hostname'] = "node.zni.lan";
	\SpringDvs\Config::$spec['password'] = "abc";
	//\SpringDvs\Config::$spec['token'] = "b7eb89f9d979f18eca9268142c868462";
	\SpringDvs\Config::$spec['token'] = "b7eb89f9d979f18eca9268142c868462";
	
	\SpringDvs\Config::$net['primary'] = "127.0.0.1";
	\SpringDvs\Config::$net['hostname'] = "gsn.zni.lan";
	\SpringDvs\Config::$net['hostres'] = "spring/";
	\SpringDvs\Config::$net['geosub'] = "local";
	\SpringDvs\Config::$net['geotop'] = "uk";
	\SpringDvs\Config::$spec['testing'] = false;
	
	\SpringDvs\Config::$sys['store'] = "/srv/www/vhosts/node.zni.lan/gsnstore";
	\SpringDvs\Config::$sys['store_live'] = "/srv/www/vhosts/node.zni.lan/gsnstore/live";
	\SpringDvs\Config::$sys['store_test'] = "/srv/www/vhosts/node.zni.lan/gsnstore/test";
	\SpringDvs\Config::$sys['api_token'] = "356b5dec60319bf6b7ef5bad97308f4a";
