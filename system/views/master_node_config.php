<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title> <?php echo count($subtitle) ? "$subtitle" : "Node Management"; ?> - Spring DVS</title>
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="/system/res/css/pure-min.css">
	<link rel="stylesheet" href="/system/res/css/nodeman.css">
	
	<script type="text/javascript" src="/system/res/js/jquery-2.2.1.js"></script>
	<script type="text/javascript" src="/system/res/js/knockout-3.4.0.js"></script>
	<script type="text/javascript" src="/system/res/js/knockout-mapping-2.4.1.js"></script>
	<?php 
	if(isset($scriptsTop)) {
		foreach($scriptsTop as $s) {
			if(substr($s, 0, 4) == "http") {
				echo "<script type=\"text/javascript\" src=\"$s\"></script>\n";
			} else {
				echo "<script async type=\"text/javascript\" src=\"/system/res/js/$s\"></script>\n";
			}
		}
	}
	?>
</head>

<body>
<img id="logo" src="/system/res/img/watermark.png">
<div id="page">
	<nav class="pure-menu custom-restricted-width">
		<span class="pure-menu-heading">spring://</span>
		
		<ul class="pure-menu-list">
	        <li class="pure-menu-item"><a href="/node/" class="pure-menu-link">Overview</a></li>
	        <li class="pure-menu-item" id="services-menu"><a href="#" class="pure-menu-link">Services</a></li>
	        <?php 
	        	foreach($services as $k => $s) {
	        		echo '<li class="pure-menu-item menu-item-sub services-sub"><a href="/node/service/network/'.$k.'" class="pure-menu-link">'.$s.'</a></li>';
	        	}
	        
	        ?>
	        
	        <li class="pure-menu-item" id="keyring-menu"><a href="javascript:void(0);" class="pure-menu-link">Keyring</a></li>

			<li class="pure-menu-item menu-item-sub keyring-sub"><a href="/node/keyring/view/" class="pure-menu-link">View</a></li>
			<li class="pure-menu-item menu-item-sub keyring-sub"><a href="/node/keyring/import/" class="pure-menu-link">Import</a></li>
			<li class="pure-menu-item menu-item-sub keyring-sub"><a href="/node/keyring/cert/" class="pure-menu-link">Private</a></li>	    
	    </ul>
	    <img class="logo" src="/system/res/img/sdvs_text_small.png">
	</nav>
	<main class="pure-g">
		<header class="content pure-u-1-1 content-gutter">
		<div class="ui-uri"><?php
			echo 'spring://' . \SpringDvs\Config::$spec['springname'] 
					. '.' . \SpringDvs\Config::$net['geosub']
					. '.' . \SpringDvs\Config::$net['geotop'];
		?></div>
		
		<div class="ui-version">v<?php
			$v = include __DIR__.'/../coreinfo.php';
			echo $v['version'];
		?></div>
		</header>
		<section class="content">
			<?php echo $body_content; ?>
		</section>
	</main>
	
</div>
	
</body>


<?php 
	if(isset($scriptsBottom)) {
		foreach($scriptsBottom as $s) {
			echo "<script type=\"text/javascript\" src=\"/system/res/js/$s\"></script>\n";
		}
	}
?>
</html>