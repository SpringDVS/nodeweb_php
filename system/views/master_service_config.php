<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Node Management - Spring DVS</title>
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
			echo "<script async type=\"text/javascript\" src=\"/system/res/js/$s\"></script>\n";
		}
	}
	?>
</head>

<body>
<img id="logo" src="/system/res/img/watermark.png">
<div id="page" class="pure-g">
	<header class="pure-u-1-1" id="header-block">
	
	</header>
	
	
	<main class="pure-u-1-1 pure-g">
		<section>
			<a href="/node/" style="font-size: 25px; margin-left: 15px; text-decoration: none;"><strong>Home</strong></a>
		</section>
		
		<?php echo $body_content; ?>
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