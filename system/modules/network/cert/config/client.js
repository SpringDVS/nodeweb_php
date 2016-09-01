/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

CertificateClient =  function() {
	var self = this;
	
	this.requestCertificate = function() {
	      $.getJSON("/node/api/nwservice/get/cert/?task=cert",{},
	    	function(obj){
	    	  if(obj.result != "ok") {
	    		  console.error("Error loading certificate");
	    		  return;
	    	  }
	    	  
	    	  $('#userid-name').text(obj.cert.name);
	    	  $('#userid-email').text(obj.cert.email);
	    	  
	    	  html = "<h4>Signatories</h4>";
	    	  html += "<ul>";
	    	  for(i = 0; i < obj.cert.sigs.length; i++) {
	    		  sig = obj.cert.sigs[i];
	    		  html += "<li>";
	    		  html += sig;	    		  
	    		  html += "</li>";
	    	  }
	    	  html += "</ul>";
	    	  $('#userid-sigs').html(html);
	    	  $('#public-key').text(obj.cert.armor);
	        });
	}
	
	this.requestOptions = function() {
		$.getJSON("/node/api/nwservice/get/cert/?task=options",{},
	    	function(obj){
	    	  if(obj.result != "ok") {
	    		  console.error("Error loading options");
	    		  return;
	    	  }
	    	  
    		  $('#option_accept_push').prop('checked', obj.options.accept_push)
	
	        });
	}
	
	this.postOptions = function() {

		post = "accept_push=" + ($('#option_accept_push').prop("checked") ? "1" : "0");

		$.post("/node/api/nwservice/post/cert/?task=options",post,
		    	function(obj){
		    	  if(obj.result != "ok") {
		    		  console.error("Error setting options");
		    		  return;
		    	  }		
		        });
	}
}

var certClient = new CertificateClient();
certClient.requestCertificate();
certClient.requestOptions();
