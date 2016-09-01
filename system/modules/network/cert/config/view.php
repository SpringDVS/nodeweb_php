<?php
/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */
if(!defined('NODE_ADMIN')) die("Platform Error");
?>

<div class="pure-u-3-5">
	
	<div class="white-container raised">
		<h3>Certificate</h3>
		The following certificate is being broadcast on the network

		<h4 id="userid-name"></h4>
		<div id="userid-email"></div>
		<div id="userid-sigs"></div>

		<div>
			<textarea id="public-key" rows="12" cols="65" class="key-display"></textarea>
		</div>

	</div>

</div>


<div class="pure-u-2-5">
 	<div class="white-container raised">
		<h3>Options</h3>
		<div class="pure-form pure-form-stacked">
 			<label for="allow_push">
            	<input id="option_accept_push" name="accept_push" type="checkbox"> Automatically accept pushed certificates
        	</label>			
        	<button href="javascript:void(0);" onclick="certClient.postOptions()">Update Options</button>
		</div>
	</div>
</div>