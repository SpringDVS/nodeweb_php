/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

var BulletinListModel = function () {

    var self = this;
    
    this.bulletins = ko.observableArray([]);
    this.requestBulletins = function() {
        $.getJSON("/node/api/nwservice/get/bulletin/?task=all",{},function(data){
            sz = data.length;
            self.bulletins.removeAll();
            for(i = 0; i < sz; i++) {
                self.bulletins.push(data[i]);
            }
            
        });
    };
    
    this.newBulletin = function(bulletin) {
        
        $.post(
            '/node/api/nwservice/push/bulletin/?task=new',
            bulletin.asString(),
            function(data){
               self.requestBulletins();
            }
        );
    }
    
    this.removeBulletin = function(e) {

        $.post(
            '/node/api/nwservice/push/bulletin/?task=rem',
            "key="+e.key,
            function(data){
               self.requestBulletins();
            }
        );

    }

    ko.applyBindings(self);
};


var Bulletin = function(title,content,tags) {
    this.title = title;
    this.tags = tags;
    this.content = content;
    
    this.asString = function() {
        return "title=" + this.title +
            "&type=" + this.type +
            "&content=" + this.content +
            "&tags=" + this.tags;
    };
}

var BulletinListForm = function (model) {
    var self = this;
    this.model = model;
    
    this.send = function () {
        
        b = new Bulletin(
                $("#bf-title").val(),
                $("#bf-content").val(),
                $("#bf-tags").val()
            );
    
        self.model.newBulletin(b);
        $("#bf-title").val("");
        $("#bf-tags").val("");
        $("#bf-content").val("");
    }
}

var buModel = new BulletinListModel()
var buForm = new BulletinListForm(buModel);
buModel.requestBulletins();