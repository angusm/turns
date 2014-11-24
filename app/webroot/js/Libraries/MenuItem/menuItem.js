//Run KendoUI on menus
define(
	[
		'kendoUI'
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