

/// ----------------------------- VARIABLES ------------------------------------

//LIBRARIES
libraries = new Array();
var loadedLibraries = new Array();

//PATHS
var fullPathname = window.location.pathname;
var pathname = window.location.pathname;
pathname = pathname.split('/').slice( 0,2 ).join('/');
var jsDirectory = pathname + '/js/';
var jsLibraryDirectory = pathname + '/js/Libraries/';







//DOCUMENT READY
//When the document is fully ready, call the main function
jQuery(document).ready( function(){

	//See if the view has defined a list of libraries, if so, get it
	if( typeof( getLibrariesToLoad ) == "function" ){
		getLibrariesToLoad();
	}

	//Add anything we always want to load
	libraries = addStandardLibraries( libraries );

	//One by one load all the necessary libraries
	loadLibraries();

});


//CORE MAIN
function coreMain(){

	//Handle progressive disclosure divs
	handleDisclosure();

	//Get down to business
	if( typeof( main ) == 'function' ){
		main();
	}

}

/// ----------------------------- LIBRARIES ------------------------------------

//FUNCTION: addStandardLibraries
//Tack on an array of libraries we always want to load for every page
//This will generally be UI type libraries
function addStandardLibraries(){
	libraries.push(
				new Array( 'Disclosure',	'handleDisclosure' )
			);

	return libraries;
}

//FUNCTION: loadLibraries
//Load a set of libraries one by one
function loadLibraries(){

	//If we still have a library to load then
	if( libraries.length > 0 ){

		//If we haven't already loaded the library, load it
		if( jQuery.inArray( libraries[0][0] + '/' + libraries[0][1] + '.js', loadedLibraries ) == -1 ){


			//Make an ajax request to load everything as plain text
			jQuery.ajax({
			        url: jsLibraryDirectory + libraries[0][0] + '/' + libraries[0][1] + '.js',
			        dataType: "text",
			        success: function (data) {
										console.log( 'Loaded->' + jsLibraryDirectory + libraries[0][0] + '/' + libraries[0][1] + '.js' );
										jQuery('div.scriptDump').append( '<script>' + data + '</script>' );
										//Add the library we loaded to the list of loaded libraries
										loadedLibraries.push( libraries[0][0] + '/' + libraries[0][1] + '.js' );
										eval(
											'if( typeof( loadDependenciesFor_'+libraries[0][0]+'_'+libraries[0][1]+' ) == "function" ){' +
												'loadDependenciesFor_'+libraries[0][0]+'_'+libraries[0][1]+'();' +
											'}'
										);
										libraries = libraries.splice( 1, (libraries.length - 1) );
										loadLibraries();
					}
			    });

		//Otherwise remove this element from the libraries array and go again
		}else{
			libraries = libraries.splice( 1, (libraries.length - 1) );
			loadLibraries();
		}

	}else{

		//Call standard functions
		coreMain();
	}

}


/// ----------------------------- UTILITY FUNCTIONS ------------------------------------


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