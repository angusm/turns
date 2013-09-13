
function manage(){


	//PUBLIC FUNCTION: handleNewRecordButton
	//This function will be called when the page is setup and attaches
	//the event listener to all the new record buttons that'll run the
	//REST call to get the JSON needed to update the page
	this.handleNewRecordButton = function(){
		
		//Throw on the listener
		jQuery( '.addNewRecord' ).click( function(){
				
				//Make the necessary call
				jQuery.getJSON('ajax/test.json', this.newRecordButtonCallbackfunction(data) );
				
		});
		
	}
	
	//PUBLIC FUNCTION: newRecordButtonCallback
	//This function will update the form with the data returned as
	//a result of the REST call to create a new record
	this.newRecordButtonCallback = function( data ){
	
		//Add the returned record to the dropdown select
		this.addRecordToDropdownSelect( data['modelName'], data['uid'], data['name'] );
		
	}
	
};