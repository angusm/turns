var turnFormManager = new manage();
turnFormManager.handleNewRecordButton();

//Object for management of a turns form
function manage(){


	//PUBLIC FUNCTION: addRecordToDropdownSelect
	//Add the created record, with the given modelName, uid and name to the
	//appropriate dropdown select box.
	this.addRecordToDropdownSelect = function( modelName, uid, name ){
		
		jQuery( '.modelRecordSelect[modelName="' + modelName + '"]' ).append(
			'<option modelName="' + modelName + '" uid="' + uid + '" name="' + name + '">' +
			name + 
			'</option>'
		);
		
	};

	//PUBLIC FUNCTION: handleNewRecordButton
	//This function will be called when the page is setup and attaches
	//the event listener to all the new record buttons that'll run the
	//REST call to get the JSON needed to update the page
	this.handleNewRecordButton = function(){
		
		//Throw on the listener
		jQuery( '.addNewRecord' ).click( function(){
				
				var modelName = 
				
				//Make the necessary call
				jQuery.getJSON('../newRecord', this.newRecordButtonCallbackfunction(data) );
				
		});
		
	};
	
	//PUBLIC FUNCTION: newRecordButtonCallback
	//This function will update the form with the data returned as
	//a result of the REST call to create a new record
	this.newRecordButtonCallback = function( data ){
	
		//Add the returned record to the dropdown select
		this.addRecordToDropdownSelect( data['modelName'], data['uid'], data['name'] );
		
	};
	
};