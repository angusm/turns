/**
 * Created by Angus on 2015-01-05.
 */

// Load everything we'll need
requirejs(
	[
		'jquery',
		'jquery-ui',
		'Angular/angular',
		'Utilities/functions'
	],
	function() {

		console.log('Loaded UnitController.js');

		// Setup the namespaces we're using
		window.AngularApps = defaultValue(window.AngularApps,{});
		window.AngularApps.Controllers = defaultValue(window.AngularApps.Controllers,{});

		// Setup the controller function
		window.AngularApps.Controllers.UnitController = function() {

		};


	}
);
