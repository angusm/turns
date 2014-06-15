

/// ----------------------------- VARIABLES ------------------------------------

//LIBRARIES
var libraries = [];
var loadedLibraries = [];

//PATHS

var pathname = window.location.pathname;
pathname = pathname.split('/').slice( 0,2 ).join('/');

var jsLibraryDirectory  = pathname + '/js/Libraries/';
var imgLibraryDirectory = pathname + '/img/'

//DOCUMENT READY
//When the document is fully ready, call the main function
jQuery(document).ready( function(){


	//Add anything we always want to load
	libraries = addStandardLibraries( libraries );
	
	//See if the view has defined a list of libraries, if so, get it
	if( typeof( getLibrariesToLoad ) == "function" ){
		getLibrariesToLoad();
	}

	//One by one load all the necessary libraries
	loadLibraries();

});

/// ----------------------------- LIBRARIES ------------------------------------

//FUNCTION: addStandardLibraries
//Tack on an array of libraries we always want to load for every page
//This will generally be UI type libraries
function addStandardLibraries(){
	libraries.push(
        ['Events',          'EventBus'],
        ['Disclosure',		'handleDisclosure'],
        ['MenuItem',        'menuItem'],
        ['SlideShow',       'slideshow'],
        ['Layout',          'default'],
        ['Authentication',  'authentication'],
        ['EditableSelect',  'editableSelect']
	);

	return libraries;
}

//FUNCTION: getLibrariesToLoad
//Load the page libraries
function getLibrariesToLoad(){
    
	//Check to see if we have pageLibraries to load for this page
	if( typeof window.pageLibraries != 'undefined' ){
		//If we do load them onto our libraries stack
		for( var i = 0; i < window.pageLibraries.length; i++ ){
			libraries.push( window.pageLibraries[i] );
		}
	}
}

//FUNCTION: loadLibraries
//Load a set of libraries one by one
function loadLibraries(){

	//If we still have a library to load then
	if( libraries.length > 0 ){

		//If we haven't already loaded the library, load it
		if( jQuery.inArray( libraries[0][0] + '/' + libraries[0][1] + '.js', loadedLibraries ) == -1 ){

			//Throw down a 
			var script  = document.createElement('script');
			script.type = 'text/javascript';
			script.src  = jsLibraryDirectory + libraries[0][0] + '/' + libraries[0][1] + '.js';

			//Append the script to the page
			jQuery('div.scriptDump').append( script );
			
			//Add the library we loaded to the list of loaded libraries
			loadedLibraries.push( libraries[0][0] + '/' + libraries[0][1] + '.js' );

            //We don't allow the '.'s in the names as it will cause the eval to treat anything after
            //the period as a function or property of an undefined value, same with slashes
            var loadDependenciesFunction = 'loadDependenciesFor_'+libraries[0][0]+'_'+libraries[0][1];
            loadDependenciesFunction = loadDependenciesFunction.replace('.', '_');
            loadDependenciesFunction = loadDependenciesFunction.replace('/', '_');

            //If the function to load dependencies exists then we load the dependencies
            if ( eval( 'typeof '+loadDependenciesFunction ) == "function") {
                eval( loadDependenciesFunction+'();' );
            }

			//Remove the library we just loaded from our list of things to load
			libraries = libraries.splice( 1, (libraries.length - 1) );
			
			//Load the next bunch of libraries
			loadLibraries();

		//Otherwise remove this element from the libraries array and go again
		}else{
			libraries = libraries.splice( 1, (libraries.length - 1) );
			loadLibraries();
		}

	}

}

/// ----------------------------- OOP FUNCTIONS ------------------------------------

//FUNCTION: extend
//Extends classes, OOP YAY!
function extend(ChildClass, ParentClass) {
    ChildClass.prototype                = new ParentClass();
    ChildClass.prototype.constructor    = ChildClass;
}

/// ----------------------------- UTILITY FUNCTIONS ------------------------------------

//FUNCTION: isBound
//Pulled from this StackOverflow question: 
//http://stackoverflow.com/questions/6361465/how-to-check-if-click-event-is-already-bound-jquery
//Check to see if the given function is bound to and element for the given eventType
//Commented with my best understanding as of Oct 5th 2013
jQuery.fn.isBound = function(eventType, callBackFunction) {

	//To do this we've got to grab some data from the jQuery library
	var eventData   = jQuery._data(this[0], 'events');
	var returnValue = false;
	
	//If there's no event data then there can't be a bound function
	if( eventData === undefined ){
		return false;	
	}
	
	//If there's nothing bound to the event, then our function can't be bound
	if( ! jQuery.inArray(eventType, eventData) || eventData[eventType] == undefined){
		return false;
	}

	//We now know there is functions bound the the given event, it's our job
	//to loop through them and see if any of them are the one we're looking for
    if( eventData[eventType].length != 0 ){
        jQuery.each( eventData[eventType], function( indexKey, value ){

            if( value['handler'] == callBackFunction ){
                returnValue = true;
            }

        });
    }

	//Return the result
    return returnValue;
	
};

//FUNCTION: isInt
//Sometimes you just want to know if a number is an integer
function isInt( something ){
	return !isNaN(parseInt(something)) && isFinite(something);
}

//FUNCTION: makeURLSafe
//Takes a given bit of text and makes it safe to pass to a URL, in this case we convert
//spaces to underscores and remove anything else we can't handle
function makeURLSafe( dangerousWord ){
    return dangerousWord
        .replace(/ /g,'_')
        .replace(/[^\w-]+/g,'')
        ;
}