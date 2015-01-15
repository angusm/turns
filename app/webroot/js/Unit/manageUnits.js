//Load everything we're going to need
requirejs(
	[
		// Replacing with Angular
		//'Knockout/KnockoutWrapper',
		//'Utilities/knockout',
		'jquery',
		'jquery-ui',
		'Angular/angular',
		'Angular/Controllers/UnitController',
		'Utilities/functions'
	],
	function() {
		console.log('Loaded manageUnits.js');


		// Setup the namespace we'll be using, deliberately clearing any pre-existing data
		window.AngularApps = defaultValue(window.AngularApps,{});
		window.AngularApps.ManageUnits = angular.module('ManageUnits',[]);
		//window.AngularApps.ManageUnits.controller(
		//	'UnitController',
		//	window.AngularApps.Controllers.UnitController
		//);

		// Initialize the page
		jQuery(document).ready(
			function() {
				console.log('Doc Ready');
			}
		);

	}
);