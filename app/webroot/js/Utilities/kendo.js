/**
 * Created by Angus on 2014-11-25.
 */
define(
	[
		'Utilities/functions'
	]
);

//Setup the namespace we'll be using for this library

window.Utilities        = defaultValue(window.Utilities,{});
window.Utilities.Kendo  = defaultValue(window.Utilities.Kendo,function(){});

/**
 * Load data into
 * @param model     Constructor function of the view model to create
 * @param dumpName  Name of the created data dump on the page
 * @return ViewModel
 */
window.Utilities.Kendo.loadModelFromDataDump = function (model,dumpName) {

	//Grab the information from the dump name and pass it to the model
	var data = $('.data-dump[data-name="'+dumpName+'"')
		.first()
		.data('dump');
	var viewModel = new model(data);

	return viewModel;
};