var KeyImporterController = function() {
	
	this.sendArmor = function() {
		armorContainer = $('#import-armor');
		data = "armor="+encodeURIComponent(armorContainer.val());

		$.post(
		    '/node/api/keyring/post/import',
		    data,
		    function(data){
		       obj = $.parseJSON(data);
		       if(obj.result != 'ok') {
		    	   $('#import-ok').text("Imported Failed!")
		    	   return;
		       }
		       $('#import-ok').text("Imported key for `"+obj.name+"`")
		    }
		);
	}
}

var KeyImporter = new KeyImporterController();