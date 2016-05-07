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
		echo setup_complete();
		return;
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
			$("#wait").show();
			geosub = $("#geosub").val()
			$.getJSON("?request="+geosub, function(data) {
				$("#wait").hide();
				if(data.hostname == "invalid") {
					alert("Error\nThe supplied Geosub `"+geosub+"` does not exist");
					return;
				}
				$("#primary-hostname").val(data.hostname);
				$("#primary-service").val(data.resource);
				$("#primary-address").val(data.address);
				
				uri = "spring://"+$("#springname").val() + "." + geosub +".uk";
				$("#uri").text(uri);
				checkEmptyElement("#geosub", "#FFFFFF");
				checkEmptyElement("#primary-hostname", "#EAEDED");
				checkEmptyElement("#primary-address", "#EAEDED");
				checkEmptyElement("#primary-service", "#EAEDED");
			});
		}
		
		function checkEmptyElement(id, defaultColor) {
			if($(id).val() == "") {
				$(id).css("background-color", "#FFBFC3");
				return false;
			} else {
				$(id).css("background-color", defaultColor);
				return true;
			}
		}
		
		function checkValidElement(id, defaultColor, pred) {
			if(pred($(id))) {
				$(id).css("background-color", defaultColor);
				return true
			} else {
				$(id).css("background-color", "#FFBFC3");
				return false;
			}
		}
		
		function validatePasswords() {
			if($("#pass").val() != $("#passcheck").val()) {
				$("#pass").css("background-color", "#FFBFC3");
				$("#passcheck").css("background-color", "#FFBFC3");
				return false;
			} else {
				$("#pass").css("background-color", "#FFFFFF");
				$("#passcheck").css("background-color", "#FFFFFF");
				return true;
			}
		}
		
		function validateForm() {
			complete = true;
			
			if(!checkEmptyElement("#pass", "#FFFFFF")) {
				complete = false;
			} else if(!validatePasswords()) {
				complete = false;
			}

			if(!checkEmptyElement("#passcheck", "#FFFFFF")) {
				complete = false;
			} else if(!validatePasswords()) {
				complete = false;
			}
			

			
			if(!checkEmptyElement("#springname", "#FFFFFF")) complete = false;
			if(!checkEmptyElement("#hostname", "#FFFFFF")) complete = false;
			
			
			if(!checkEmptyElement("#geosub", "#FFFFFF")) complete = false;
			if(!checkEmptyElement("#primary-hostname", "#EAEDED")) complete = false;
			if(!checkEmptyElement("#primary-address", "#EAEDED")) complete = false;
			if(!checkEmptyElement("#primary-service", "#EAEDED")) complete = false;
			
			if(!checkEmptyElement("#token", "#FFFFFF")) complete = false;
			
			if(!checkValidElement("#token", "#FFFFFF", function(element){
				if(element.val().length != 32) return false;
				
				return true;
			}));

			if(!checkEmptyElement("#store", "#FFFFFF")) complete = false;
			
			if(!complete) {
				$("#errors").text("There are errors in the form");
				$("#success").text("");
				return false;
			} else {
				$("#errors").text("");
				$("#success").text("Form Valid");
			}
			
			return true;
		}
		
		function generate() {
			
			if(!validateForm()) return;
			

			
	
			postStr = "springname=" + $("#springname").val()
				+ "&hostname=" + $("#hostname").val()
				+ "&pass=" + $("#pass").val()
				+ "&passcheck=" + $("#passcheck").val()
				+ "&geosub=" + $("#geosub").val()
				+ "&primary-hostname=" + $("#primary-hostname").val()
				+ "&primary-service=" + $("#primary-service").val()
				+ "&primary-address=" + $("#primary-address").val()
				+ "&token=" + $("#token").val()
				+ "&store=" + $("#store").val();
		
			$.post("?generate=true",postStr, function(data) {
				if(data == "ok"){
					alert("Generated config successfully");
					$("#success").text("Generated Configuration");
				} else {
					alert("Error writing config file!\nCheck permissions on directory");
					
				}
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
			This script is used to generatate the configuration for your node.
			It is <em>unimaginably</em> important that you delete this script file once you've set 
			it up. If you need to set it up again, you can download it from the
			spring-dvs.org or edit the config manually.
			<div class="pure-form pure-form-stacked" style="margin-top: 30px;">
				<fieldset class="pure-g">
					<legend class="pure-u-1-1"><strong>Local Node</strong></legend>
					
					<section class="pure-u-1-4">
						
						<label for="springname">Springname:</label>
						<input id="springname" type="text" placeholder="">
						
					</section>
					
					<aside class='pure-u-16-24'>
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
						<input id="hostname" type="text" placeholder="" value="<?php
						echo $_SERVER['HTTP_HOST'];
						?>">
						
					</section>
		
					<aside class='pure-u-16-24'>
						<div class="note">
							The <strong>Hostname</strong> is the same as this
							web site's address, which will be used to access this node.
							If your site address is <em>http://www.example.org</em> then 
							your hostname will be <em>example.org</em>.
							If your site address is <em>http://sub.example.org</em> then 
							your hostname will be <em>sub.example.org</em>.
						</div>
					</aside>
					
					<div class="pure-1-1">&nbsp;</div>		
					<section class="pure-u-1-4">
						
						<label for="pass">Password:</label>
						<input id="pass" type="password" placeholder="">
						<label for="pass">Recheck:</label>
						<input id="passcheck" type="password" placeholder="">
						
					</section>
		
					<aside class='pure-u-16-24'>
						<div class="note">
							This will be the <strong>Password</strong> for the 
							node administrator account
						</div>
					</aside>
					
					

					<legend class="pure-u-1-1" style="margin-top: 100px;"><strong>Network</strong></legend>
					
					<section class="pure-u-1-4">
						
						<label for="geosub">Geosub Network:</label>
						<input id="geosub" type="text" placeholder="">
						<button onclick="getGeosubDetails()">Pull Details</button>
						<img id="wait" src="/system/res/img/wait.gif" style="display: none;">
					</section>
					
					<aside class='pure-u-16-24'>
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
		
					<aside class='pure-u-16-24'>
						<div class="note">
							The primary details for the network are filled out
							automatically from the Spring network root server
						</div>
					</aside>
					
					<div class="pure-1-1" style="clear: both;">&nbsp</div>
					
					<section class="pure-u-1-4">
						
						<label for="token">Token:</label>
						<textarea id="token"></textarea>
						
					</section>
		
					<aside class='pure-u-16-24'>
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
					
					<div class="pure-1-1" style="clear: both;">&nbsp</div>
					<legend class="pure-u-1-1"><strong>Database</strong></legend>
					<section class="pure-u-1-4">
						
						<label for="store">Store:</label>
						<textarea id="store"><?php 
						echo __DIR__; 
						?></textarea>
						
					</section>
		
					<aside class='pure-u-16-24'>
						<div class="note">
							The <strong>Store</strong> location is where the node's
							self-contained database is held. This <em>must</em> be
							outside the <code>public_html</code> folder (or equivalent), 
							usually the directory above. The current directory 
							is provided as a start.
						</div>
					</aside>
					
				</fieldset>
				
				<button onclick="generate()">Generate Configuration</button>
				Note: This will overwrite any previous configuration
				<div id="errors" style="color: #b94a48; font-weight: bold;">&nbsp;</div>
				<div id="success" style="color: #1D5F12; font-weight: bold;"></div>
			</div>
			</div>
	
			</div>

			
		<?php
		return ob_get_clean();
	}
	
	function setup_complete() {
		$springname = filter_input(INPUT_POST, 'springname');
		$hostname = filter_input(INPUT_POST, 'hostname');
		$pass = filter_input(INPUT_POST, 'pass');
		$passcheck = filter_input(INPUT_POST, 'passcheck');
		$geosub = filter_input(INPUT_POST, 'geosub');
		$primaryHostname = filter_input(INPUT_POST, 'primary-hostname');
		$primaryAddress = filter_input(INPUT_POST, 'primary-address');
		$primaryService = filter_input(INPUT_POST, 'primary-service');
		$token = filter_input(INPUT_POST, 'token');
		$store = filter_input(INPUT_POST, 'store');
		$store .= "/gsnstore";
		$text = "
<?php
	\SpringDvs\Config::\$spec['springname'] = \"$springname\";
	\SpringDvs\Config::\$spec['hostname'] = \"$hostname\";
	\SpringDvs\Config::\$spec['password'] = \"$pass\";
	\SpringDvs\Config::\$spec['token'] = \"$token\";

	\SpringDvs\Config::\$net['master'] = \"$primaryAddress\";
	\SpringDvs\Config::\$net['hostname'] = \"$primaryHostname\";
	\SpringDvs\Config::\$net['hostres'] = \"$primaryService\";
	\SpringDvs\Config::\$net['geosub'] = \"$geosub\";
	\SpringDvs\Config::\$net['geotop'] = \"uk\";
	\SpringDvs\Config::\$spec['testing'] = false;
	
	\SpringDvs\Config::\$sys['store'] = \"$store\";
	\SpringDvs\Config::\$sys['store_live'] = \"$store/live\";
	\SpringDvs\Config::\$sys['store_test'] = \"$store/test\";
";
		$fp = fopen("system/config.php", 'w');
		if(!$fp) {
			return "error";
		}
		fwrite($fp, $text);
		fclose($fp);
		mkdir($store);
		mkdir($store."/test");
		mkdir($store."/live");
		return "ok";
		
	}
?>