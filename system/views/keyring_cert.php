<div class="pure-u-1-1 content-gutter">

<div id="key-gen-panel" class="white-container raised" style="display: none;">
<div id="error-msg" style="color: red; font-weight: bold;"></div>
<br>
<div id="key-gen-form">

	<input type="text" id="email-input" placeholder="Email">
	<input type="password" id="passphrase-input" placeholder="Passphrase">
	<button style="margin-left: 10px;" onclick="KeyCert.requestGeneration()">Generate Keys</button>
	
	<h4>or</h4>
	
	if you already have keys ready <a href="javascript:void(0)" onclick="KeyCert.switchForms()">Switch to upload</a>

</div>

<div id="key-upload-form" style="display: none;">
	<textarea rows="15" cols="65" id="private-key-input" class="key-display" placeholder="Private Key ASCII Armor"></textarea>
	<textarea rows="15" cols="65" id="public-key-input" class="key-display" placeholder="Public Key ASCII Armor"></textarea><br>
	<button style="margin-top: 10px;" onclick="KeyCert.uploadKeys()">Upload Keys</button>
	<h4>or</h4>
	
	if you need to generate keys <a href="javascript:void(0)" onclick="KeyCert.switchForms()">Switch to generator</a>	
</div>

</div>
</div>
<div id="key-display">
	<div class="pure-u-1-2 content-gutter">
		<div class="">
			<h3>Private Key</h3>
			<textarea rows="15" cols="65" id="private-key-display" class="key-display"></textarea>
			
		</div>
		<div class="">
			<h3>Public Key</h3>
			<textarea rows="15" cols="65" id="public-key-display" class="key-display"></textarea>
		</div>
	</div>
	<div class="pure-u-1-2 content-gutter">
	<div class="">
		<h3>Certificate</h3>
		<h4 id="userid-name">Invalid</h4>
		<div id="userid-email">Invalid</div>
		
		<div style="margin-top: 30px;">
		<strong>Signatories</strong>
		<ul id="userid-sigs"></ul>
		</div>
	</div>
	</div>

</div>