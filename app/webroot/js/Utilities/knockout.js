// Replacing with Anguilar

///**
// * Created by Angus on 2014-11-25.
// */
//define(
//	[
//		'Inflection/inflection',
//		'Utilities/functions'
//	]
//);
//
////Setup the namespace we'll be using for this library
//
//window.Utilities            = defaultValue(window.Utilities,{});
//window.Utilities.Knockout   = defaultValue(window.Utilities.Knockout,function(){});
//
///**
// * Load data into
// * @param model     Constructor function of the view model to create
// * @param dumpName  Name of the created data dump on the page
// * @return ViewModel
// */
//window.Utilities.Knockout.loadModelFromDataDump = function (modelInstance,dumpName) {
//
//	// Create the object that we'll return
//	var object = {};
//
//	// Grab the information from the dump name and pass it to the model
//	var data = $('.data-dump[data-name="'+dumpName+'"]')
//		.first()
//		.data('dump');
//
//	// Start looping through the information in the data
//	for (var key in data) {
//
//		var value = data[key];
//
//		// Check if the object is a plural
//		if (isInt(key)) {
//
//			// Parse the subobject
//			var returnedObject = window.Utilities.Knockout.loadModelFromDataDump(value);
//			// Merge it with the current object
//			for( var subKey in returnedObject){
//				// Get the pluralized name for the returned object
//				// (we'll be storing all associations of this type as plurals)
//				pluralizedName = pluralize(subKey);
//
//				// Set it up as a default object
//				eval('object.'+pluralizedName+' = defaultValue(object.'+pluralizedName+', []);');
//
//				// Grab a reference to the object so we don't have
//				// to keep running evals
//				var subObject = eval('object.'+pluralizedName);
//				subObject.push(returnedObject);
//
//			}
//
//		// If the value has a proper key then we must determine if it is an object or a value
//		// and assign it appropriately
//		} else {
//
//			//Get a reference to the value we wish to assign
//			var subObject = eval('object.'+key);
//
//			//If the value is an object we call this recursively and assign the object
//			if (typeof value === 'object'){
//				subObject = window.Utilities.Knockout.loadModelFromDataDump(value);
//			} else {
//				subObject = value;
//			}
//
//		}
//
//		//Return the result
//		console.log(object);
//		return object;
//
//	}
//
//	var viewModel = new modelInstance(data);
//
//	return viewModel;
//};