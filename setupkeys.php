<?php

$generate = filter_input(INPUT_GET, 'generate');

	if($generate) {
		define('NODE_KEYGEN', 'true');
		require 'vendor/autoload.php';
		require 'autoload.php';
		include 'system/config.php';
		
		$handler = new KeyringHandler();
		$name = \SpringDvs\nodeurl_from_config();
		if(strlen($name) == 0) die("error no name");
		
		$email = filter_input(INPUT_POST, 'email');
		if(!$email) die("error no userid");
		
		$passphrase = filter_input(INPUT_POST, 'passphrase');
		if(!$passphrase) die("error no passphrase");
		
		$handler->generateKey($name, $email, $passphrase);
		
		echo "ok ";
		return;
	} else {
		$body_content = setup_form();
	}
	


	
?>
<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Key Setup - Spring DVS</title>
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
		function RequestGeneration() {
			if(!validateForm()) return;
			email = $('#userid-email').val();
			passphrase = $('#passphrase').val();
			post = "email="+email+"&passphrase="+passphrase;
			
			$.post("?generate=true",post, function(data) {
				if(data == "ok"){
					$("#success").text("Generated Keys");
					
				} else {
					$("#error").text("Error generated Keys");
				}
			})
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
		
		function validatePasswords() {
			if($("#passphrase").val() != $("#passphrase-recheck").val()) {
				$("#passphrase").css("background-color", "#FFBFC3");
				$("#passphrase-recheck").css("background-color", "#FFBFC3");
				return false;
			} else {
				$("#passphrase").css("background-color", "#FFFFFF");
				$("#passphrase-recheck").css("background-color", "#FFFFFF");
				return true;
			}
		}

		function validateForm() {
			complete = true;
			
			if(!checkEmptyElement("#passphrase", "#FFFFFF")) {
				complete = false;
			} else if(!validatePasswords()) {
				complete = false;
			}

			if(!checkEmptyElement("#userid-email", "#FFFFFF")) complete = false;
			
			if(!complete) {
				$("#errors").text("There are errors in the form");
				$("#success").text("");
				return false;
			} else {
				$("#errors").text("");
				$("#success").text("Form Valid");
				return true;
			}
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
			This form is for automated key generation. If you have already generated your own keys
			for the node then please skip this stage of the setup and upload your keys in the admin
			panel <em>Keyring -&gt; Private</em>. <strong>If you're not sure</strong>: use this form to generate the keys.
			<div class="pure-form pure-form-stacked" style="margin-top: 30px;">
				<fieldset class="pure-g">
					<legend class="pure-u-1-1"><strong>Private Key and Certificate</strong></legend>
					
					<section class="pure-u-1-4">
						
						<label for="springname">Contact Email:</label>
						<input id="userid-email" type="text" placeholder="email">
						
					</section>
					
					<aside class='pure-u-16-24'>
						<div class="note">
							The <strong>Contact Email</strong> is placed on your node's
							certificate. This can be seen by other people who view the
							certificate and can be used as a contact address for the 
							organisation that is running the node.
						</div>
					</aside>
					<div class="pure-1-1">&nbsp;</div>
					
	
					<section class="pure-u-1-4">
						
						<label for="springname">Passphrase:</label>
						<input id="passphrase" type="password" placeholder="Passphrase">
						<label for="springname">Recheck:</label>
						<input id="passphrase-recheck" type="password" placeholder="Re-enter passphrase">
						
					</section>
					
					<aside class='pure-u-16-24'>
						<div class="note">
							The <strong>Passphrase</strong> you choose here is used to 
							unlock the node's private key for signing digital signatures 
							or decrypting data. It is important to not lose this passphrase 
							as there is no means for recovery. If this passphrase is lost, 
							a new key will have to be generated along with a fresh certificate.
						</div>
					</aside>
					<div class="pure-1-1">&nbsp;</div>
				</fieldset>
				
				<button href="javascript:void(0);" onclick="RequestGeneration()">Generate Keys</button>
				<div style="margin-top: 10px;">
					<strong>Note:</strong> This process may take a minute
				</div>
				<div id="errors" style="color: #b94a48; font-weight: bold;">&nbsp;</div>
				<div id="success" style="color: #1D5F12; font-weight: bold;"></div>
			</div>
			</div>
			
		<?php
		return ob_get_clean();
	}
	

?>