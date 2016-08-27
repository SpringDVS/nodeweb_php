
var KeyringViewController = function() {
	var self = this;
	this.requestAll = function() {
		$.get(
		    '/node/api/keyring/get/all',
		    function(data){
		    	listing = $.parseJSON(data);
		    	
		    	size = listing.length;
		    	html  = ""
		    	for(i = 0; i < size; i++) {
		    		tmp = [
		    		       '<tr>',
		    		       '<td>',
		    		       '<a href="javascript:void(0)" id="select-'+listing[i][0]+'" onclick="krview.requestKey(`'+listing[i][0]+'`)">'+listing[i][1]+'</a>',
		    		       '</td>',
		    		       '</tr>',
		    		       '<tr id="row-'+listing[i][0]+'" class="key-info"><td id="key-'+listing[i][0]+'"></td></tr>',
		    		       ].join("\n");
		    		html += tmp;
		    	}

		    	$('#key-list').html(html);
		    }
		);
	}
	
	this.requestKey = function(id) {

		$.get(
				'/node/api/keyring/get/'+id,
				
			    function(data){
			    	response = $.parseJSON(data);
			    	if(response.result != "ok") {
			    		return;
			    	}

			    	
			    	html = "<h3>" + response.key.name + "</h3>";
			    	html += '<div style="float: left;">';
			    	html += response.key.email;
			    	html += "<h4>Certificate Signatures</h4><ul>";
			    	for(i = 0; i < response.key.sigs.length; i++) {
			    		html += "<li>" + response.key.sigs[i][0] + "&nbsp;&nbsp;(<em>"+response.key.sigs[i][1]+"</em>)</li>";
			    	}
			    	html += "</ul>";
			    	html += "</div>";

			    	html += '<div style="float: right">';
			    		html += '<textarea cols="66" rows="15" class="key-display right">' + response.key.public + '</textarea>';
			    		html += '<div class="key-id-tag">' + id + "</div>";
			    		
			    		html += '<div style="margin-top: 10px;" class="right clear">';
			    			html += '<button class="pure-button risky" onclick="krview.removeKey(`'+id+'`)">Delete</button>';
			    		html += '</div>';
			    		
			    	html += "</div>";

			    	$(".key-info").hide();
			    	$('#key-'+id).html(html);
			    	$('#row-'+id).show();
			    }
			);
	}
	
	this.removeKey = function(id) {
		if(!confirm("Are you sure you want to remove this key?")) {
			return;
		}

		$.get(
				'/node/api/keyring/post/remove/?keyid='+id,
				
			    function(data){
					self.requestAll();
			    }
			);
	}
}

var krview = new KeyringViewController();
krview.requestAll();