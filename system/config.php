
<?php
	\SpringDvs\Config::$spec['springname'] = "cci";
	\SpringDvs\Config::$spec['hostname'] = "node.zni.lan";
	\SpringDvs\Config::$spec['password'] = "a";
	\SpringDvs\Config::$spec['token'] = "abcdef";

	\SpringDvs\Config::$net['master'] = "127.0.0.1";
	\SpringDvs\Config::$net['hostname'] = "local.root";
	\SpringDvs\Config::$net['hostres'] = "spring/";
	\SpringDvs\Config::$net['geosub'] = "local";
	\SpringDvs\Config::$net['geotop'] = "uk";
	\SpringDvs\Config::$spec['testing'] = false;
	
	\SpringDvs\Config::$sys['store'] = "/srv/www/vhosts/node.zni.lan/gsnstore";
	\SpringDvs\Config::$sys['store_live'] = "/srv/www/vhosts/node.zni.lan/gsnstore/live";
	\SpringDvs\Config::$sys['store_test'] = "/srv/www/vhosts/node.zni.lan/gsnstore/test";
	\SpringDvs\Config::$sys['api_token'] = "0395b6911e620cd030164414d9c2cba2";
