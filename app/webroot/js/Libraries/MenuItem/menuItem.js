//Run KendoUI on menus
require(
	[
		'//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
		'//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js',
		window.Paths.jsDir + 'KendoUI/kendo.ui.core.min.js'
	],
	function(){
		//Add the kendoMenu to everything on the screen now
		jQuery('.menu').kendoMenu();

		//Add it to anything to come
		jQuery('#container').on('DOMNodeInserted',function(e){
			if (jQuery(e.target).is('.menu')) {
				jQuery(e.target).kendoMenu();
			}
		});

	}
);