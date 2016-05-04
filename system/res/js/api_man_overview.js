
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
            if(data.type == 34) {
                switch(data.content.status) {
                    case 0: status = "Disabled"; break;
                    case 1: status = "Enabled"; break;
                }
            }
            
            ManOverviewController.viewModel.status = status;
            node = $("#bind-status").get(0);            
            ko.cleanNode(node);
            ko.applyBindings(ManOverviewController.viewModel, node);
            if(status == "Disabled") {
                $("#action-status-update").text("Bring Online");
                $("#action-status-update").click(function() {
                   console.log("Updating");
                   $.getJSON('/node/api/state/push?state=enabled', function(data) {});
                   ManOverviewController.refreshStatus();
                });
            } else {
                $("#action-status-update").text("Bring Offline");
                $("#action-status-update").click(function() {
                   console.log("Updating");
                   $.getJSON('/node/api/state/push?state=disabled', function(data) {
                       ManOverviewController.refreshStatus();
                   });
                });
            }
        });
    },
    init: function() {
        $.getJSON('/node/api/overview/get', function(data) {
            console.log(data);
            ManOverviewController.viewModel =  ko.mapping.fromJS(data);
            ko.applyBindings( ManOverviewController.viewModel );
            ManOverviewController.refreshStatus();
        });
    },
    
    enableNode: function() {
        $.getJSON('/node/api/state/push')
    },

};

ManOverviewController.init();




