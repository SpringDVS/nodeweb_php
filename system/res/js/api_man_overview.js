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
            if(data.type == 34) {
                switch(data.content.status) {
                    case 0: status = "Disabled"; break;
                    case 1: status = "Enabled"; break;
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
        });
    },
    
    actionRegister: function() {
        
       $.getJSON('/node/api/register/push/', function(data) {
            $("#action-error").text("");
            if(data.type == 30
            && data.frame == "FrameResponse"
            && data.content.code == 200) {
                ManOverviewController.refreshStatus();
            } else {
                $("#action-error").text("Error registering on network");
            }
        });       
    }
};

ManOverviewController.init();




