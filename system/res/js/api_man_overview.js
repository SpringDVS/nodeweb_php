
var ManOverviewController = {
    self: this,
    viewModel: null,

    refreshDetails: function() {
        $.getJSON('/node/api/overview/get', function(data) {
            ManOverviewController.viewModel =  ko.mapping.fromJS(data);
            console.log("Refreshed");
        });
    },

    init: function() {
        $.getJSON('/node/api/overview/get', function(data) {
            ManOverviewController.viewModel =  ko.mapping.fromJS(data);
            ko.applyBindings( ManOverviewController.viewModel );
        });
        
    },
    

};

ManOverviewController.init();




