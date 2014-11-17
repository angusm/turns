require(
	[
		'//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
		'//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
		window.Paths.jsDir + 'KendoUI/kendo.ui.core.min.js',
		window.Paths.jsDir + 'KendoModels/KendoModels.js'
	],
	function(){

		//Setup the KendoModel for the Unit
		window.KendoModels.Unit = kendo.data.Model.define({
			fields:{
				'unit_types_uid': {
					type: 'string'
				},
				'name': {
					type: 'string'
				},
				'quantity': {
					type: 'int'
				},
				'teamcost': {
					type: 'int'
				}
			}
		});
	}
);
