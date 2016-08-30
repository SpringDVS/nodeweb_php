var KeyCertController = function() {
	var self = this
	
	this.requestPrivate = function() {
		$('#key-gen-panel').hide();
		$.get(
		    '/node/api/keyring/get/private',
		    function(data){
		       obj = $.parseJSON(data);
		       if(obj.result != 'ok') {
		    	   $('#error-msg').text("Error - node does not have a private key or certificate");
		    	   $('#key-gen-panel').show();
		    	   $('#key-display').hide();
		    	   return;
		       }
		       $('#private-key-display').val(obj.key);
		       $('#key-display').show();
		    }
		);
	}
	
	this.requestPublic = function () {
		$.get(
		    '/node/api/keyring/get/this',
		    function(data){
		       obj = $.parseJSON(data);
		       if(obj.result != 'ok') {
		    	   $('#error-msg').text("Error - node does not have a private key or certificate");
		    	   $('#key-gen-panel').show();
		    	   return;
		       }
		       $('#public-key-display').val(obj.key.armor);
		       $('#userid-name').text(obj.key.name);
		       $('#userid-email').text(obj.key.email);
		       
		       html = "";
		       for(i = 0; i < obj.key.sigs.length; i++) {
		    	   html += "<li>" + obj.key.sigs[i][0] + "&nbsp;&nbsp;(<em>"+obj.key.sigs[i][1]+"</em>)</li>";
		       }
		       
		       $('#userid-sigs').html(html);
		    }
		);		
	}
	
	this.requestKeys = function() {
		self.requestPrivate();
		self.requestPublic();
	}
	
	this.requestGeneration = function() {
		email = $('#email-input').val();
		passphrase = $('#passphrase-input').val();
		data = "email="+email+"&passphrase="+passphrase;

		$.post(
			    '/node/api/keyring/post/generate',
			    data,
			    function(data){
			       obj = $.parseJSON(data);
			       if(obj.result != 'ok') {
			    	   $('#error-msg').text("Error - failed to generate key");
			    	   $('#key-gen-panel').show();
			    	   return;
			       }
			      self.requestKeys();
			    }
			);		
	}
	
	this.uploadKeys = function () {
		pub = $('#public-key-input').val();
		pri = $('#private-key-input').val();
		data = "public="+encodeURIComponent(pub)+"&private="+encodeURIComponent(pri);

		$.post(
			    '/node/api/keyring/post/private',
			    data,
			    function(data){
			       obj = $.parseJSON(data);
			       if(obj.result != 'ok') {
			    	   $('#error-msg').text("Error - failed to upload keys");
			    	   $('#key-gen-panel').show();
			    	   return;
			       }
			      self.requestKeys();
			    }
			);				
	}
	
	this.switchForms = function () {
		
		if($('#key-gen-form').css('display') != 'none') {
			$('#key-gen-form').hide();
			$('#key-upload-form').show();
		} else {
			$('#key-gen-form').show();
			$('#key-upload-form').hide();
		}
		
	}
}

var KeyCert = new KeyCertController();
KeyCert.requestKeys();