/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

var OrgProfileModel = function () {

    var self = this;
    
    this.name = ko.observable();
    this.website = ko.observable();
    this.tags = ko.observable();
    
    this.requestProfile = function() {
        $.getJSON("/node/api/nwservice/get/orgprofile/?task=profile",{},function(data){

            console.dir(data);
            
            self.name(data.name);
            self.website(data.website);
            self.tags(data.tags);
        });
    };
    
    this.updateProfile = function(profile) {
        console.log(profile.asString());
        $.post(
            '/node/api/nwservice/push/orgprofile/?task=update',
            profile.asString(),    
            function(data){
               self.requestProfile();
            }
        );
    }
    
 

    ko.applyBindings(self);
};


var Profile = function(name, website, tags) {
    this.name = name;
    this.website = website;
    this.tags = tags;
    
    this.asString = function() {
        return "name=" + this.name +
            "&website=" + this.website +
            "&tags=" + this.tags;
    };
}

var ProfileForm = function (model) {
    var self = this;
    this.model = model;
    
    this.send = function () {
        
        b = new Profile(
                $("#bf-name").val(),
                $("#bf-website").val(),
                $("#bf-tags").val()
            );
    
        self.model.updateProfile(b);
        
    }
}

var prModel = new OrgProfileModel()
var prForm = new ProfileForm(prModel);
prModel.requestProfile();