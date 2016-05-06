<?php

	if(filter_input(INPUT_GET, 'request')) {
		$url = "http://resolve.spring-dvs.org/geosubs/". filter_input(INPUT_GET, 'request');
		echo file_get_contents($url);
		die();
	}
	
	$body_content = "";
	if(!filter_input(INPUT_GET, 'generate')) {
		$body_content = setup_form();
	} else {
		$body_content = setup_complete();
	}
?>
<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Setup - Spring DVS</title>
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="/system/res/css/pure-min.css">
	<link rel="stylesheet" href="/system/res/css/nodeman.css">
	<style>
		div.note {
			margin: 15px;
		}
		input[type="text"]:disabled {
			color: #3F3F3F !important;
		}
		
		textarea {
			resize: vertical;
		}
	</style>
	
	<script type="text/javascript" src="/system/res/js/jquery-2.2.1.js"></script>
	<script type="text/javascript">
		function getGeosubDetails() {
			geosub = $("#geosub").val()
			$.getJSON("?request="+geosub, function(data) {
				if(data.hostname == "invalid") {
					alert("Error\nThe supplied Geosub `"+geosub+"` does not exist");
					return;
				}
				$("#primary-hostname").val(data.hostname);
				$("#primary-service").val(data.resource);
				$("#primary-address").val(data.address);
				
				uri = "spring://"+$("#springname").val() + "." + geosub +".uk";
				$("#uri").text(uri);
			});
		}
	</script>
		
</head>

<body>

<img id="logo" src="/system/res/img/watermark.png">
<div id="page" class="pure-g">
	<header class="pure-u-1-1" id="header-block">
	</header>
	<main class="pure-u-1-1 pure-g">
		<section>			
			<noscript><strong style="color: red;">You need Javascript enabled to configure the node</strong></noscript> 
		</section>
		
		<?php echo $body_content; ?>
	</main>
</div>
	
</body>
</html>

<?php
	function setup_form() {
		ob_start();
		?>
			<div class="pure-u-3-24">&nbsp;</div>
			<div class="pure-u-18-24">
	
			<div class="white-container raised">
			<h3>Configuration</h3>
			<div class="pure-form pure-form-stacked">
				<fieldset class="pure-g">
					<legend class="pure-u-1-1"><strong>Local Node</strong></legend>
					
					<section class="pure-u-1-4">
						
						<label for="springname">Springname:</label>
						<input id="springname" type="text" placeholder="">
						
					</section>
					
					<aside class='pure-u-3-4'>
						<div class="note">
							The <strong>Springname</strong> is the unique name of this node
							on the Spring network.
							It is suggested that the Springname is a similar or short 
							hand version of the organisation this node is representing so
							it is easy to remember. <em>Only</em> Alphanumeric characters and hyphens
							are valid
						</div>
					</aside>
					<div class="pure-1-1">&nbsp;</div>		
					<section class="pure-u-1-4">
						
						<label for="hostname">Hostname:</label>
						<input id="hostname" type="text" placeholder="">
						
					</section>
		
					<aside class='pure-u-3-4'>
						<div class="note">
							The <strong>Hostname</strong> is the same as this
							web site's address, which will be used to access this node.
							If your site address is <em>http://www.example.org</em> then 
							your hostname will be <em>example.org</em>.
							If your site address is <em>http://sub.example.org</em> then 
							your hostname will be <em>sub.example.org</em>.
						</div>
					</aside>
					
					<legend class="pure-u-1-1"><strong>Network</strong></legend>
					
					<section class="pure-u-1-4">
						
						<label for="geosub">Geosub Network:</label>
						<input id="geosub" type="text" placeholder="">
						<button onclick="getGeosubDetails()">Pull Details</button>
					</section>
					
					<aside class='pure-u-3-4'>
						<div class="note">
							The <strong>Geosub</strong> is the local geographical network that
							you are joining.
						</div>
					</aside>
					
					<div class="pure-1-1" style="clear: both;">&nbsp</div>

					<section class="pure-u-1-4">
						
						
						<input id="primary-hostname" type="text" placeholder="Primary Hostname" disabled>
						<input id="primary-address" type="text" placeholder="Primary Address" disabled>
						<input id="primary-service" type="text" placeholder="Service Resource" disabled>
						
					</section>
		
					<aside class='pure-u-3-4'>
						<div class="note">
							The primary details for the network are filled out
							automatically from the Spring server
						</div>
					</aside>
					
					<div class="pure-1-1" style="clear: both;">&nbsp</div>
					
					<section class="pure-u-1-4">
						
						<label for="token">Token:</label>
						<textarea id="token"></textarea>
						
					</section>
		
					<aside class='pure-u-3-4'>
						<div class="note">
							The <strong>Token</strong> is a 32  character string 
							that is used to validate the registration with the 
							network. This token should have been given to you 
							when you applied for registration.
						</div>
					</aside>
					
					<div class="pure-1-1" style="clear: both;">&nbsp</div>
					<legend class="pure-u-1-1"><strong>URI</strong></legend>
					<section class="pure-u-1-1">
						
						<strong id="uri">spring://</strong>
						<div class="note">
						This will be the URI for your node on the Spring network
						</div>
						
					</section>
				</fieldset>
				
				<button>Generate Configuration</button>
			</div>
			</div>
	
			</div>

		<?php
		return ob_get_clean();
	}
	
	function setup_complete() {
		return "Blah2";
	}
?>