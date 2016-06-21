/* Notice:  Copyright 2016, The Care Connections Initiative c.i.c.
 * Author:  Charlie Fyvie-Gauld <cfg@zunautica.org>
 * License: Apache License, Version 2 (http://www.apache.org/licenses/LICENSE-2.0)
 */

var ManOverviewController = {
    self: this,
    viewModel: null,
    refreshDetails: function() {
        $.getJSON('/node/api/overview/get', function(data) {
            ManOverviewController.viewModel =  ko.mapping.fromJS(data);
        });
    },

    refreshStatus: function () {

        $.getJSON('/node/api/state/get', function(data) {
            status = "Unknown";
            reg = "Unregistered"
            	
            if(data.code == "200" && data.type == "node") {
                if(data.content.state == "disabled") {
                    status = "Disabled";
                } else {
                    status = "Enabled";
                }
                reg = "Registered";
            }
            
            if(reg != "Unregistered") {
                $("#action-register").hide();
            } else {
                $("#action-register").click(ManOverviewController.actionRegister);
            }
            
            ManOverviewController.viewModel.status = status;
            ManOverviewController.viewModel.register = reg;
            
            node = $("#bind-status").get(0);
            ko.cleanNode(node);
            ko.applyBindings(ManOverviewController.viewModel, node);
            
            rnode = $("#bind-register").get(0);
            ko.cleanNode(rnode);
            ko.applyBindings(ManOverviewController.viewModel, rnode);

            button = $("#action-status-update");
            button.off();

            if(status == "Enabled") {
                button.text("Bring Offline");
                button.click(function() {
                   $.getJSON('/node/api/state/push?state=disabled', function(data) {;
                        ManOverviewController.refreshStatus();
                   });
                });
            } else {
                button.text("Bring Online");
                button.click(function() {
                   $.getJSON('/node/api/state/push?state=enabled', function(data) {
                       ManOverviewController.refreshStatus();
                   });
                });
            }
        });
    },

    init: function() {
        $.getJSON('/node/api/overview/get', function(data) {
            ManOverviewController.viewModel =  ko.mapping.fromJS(data);
            ko.applyBindings( ManOverviewController.viewModel );
            ManOverviewController.refreshStatus();
            ManOverviewController.requestServices();
            ManOverviewController.requestUpdates();
        });
        
        
    },
    
    actionRegister: function() {

       $.getJSON('/node/api/register/push/', function(data) {
            $("#action-error").text("");
            
            if(data.code == "200") {
                ManOverviewController.refreshStatus();
            } else {
                $("#action-error").text("Error registering on network");
            }
        });    
    },
    
    requestServices: function () {
        
        $.getJSON('/node/api/nwservices/pull/', function(data) {
            ManOverviewController.viewModel.nwservices(data);
        }); 
        
        $.getJSON('/node/api/gwservices/pull/', function(data) {
            ManOverviewController.viewModel.gwservices(data);
        }); 
    },
    
   requestUpdates: function () {
        
        $.getJSON('/node/api/updates/pull/', function(data) {
            ManOverviewController.viewModel.updates(data);

            if(data[0].modules.length > 0 || data[1].modules.length > 0 || data[2].modules.length > 0) {
                $("#updater").show();
                $("#update-msg").text("");
                $("#update-list").show();
            } else {
                $("#updater").hide();
                $("#update-list").hide();
                $("#update-msg").text("Fully up-to-date!");
            }
        }); 
    },
    
    performUpdate: function() {
        $.getJSON('/node/api/updates/push/', function(data) {
            ManOverviewController.requestUpdates();
        }); 
    }
    
};

ManOverviewController.init();
