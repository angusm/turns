define(
	[
		'jquery',
		'jquery-ui',
		'knockout',
		'Utilities/functions'
	]
);

window.ViewModel = defaultValue(window.ViewModel,{});

//View model for units
window.ViewModel.Unit = function (data) {

		//Setup the properties
		var self = this;
		self.quantity   = ko.observable(data.quantity);
		self.uid        = ko.observable(data.uid);

		//Setup the relationships

		//Belongs to
		self.UnitType   = ko.observable();
		self.User       = ko.observable();

};