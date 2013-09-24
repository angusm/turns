//PEPPER POTTS FUNCTION: handleEverything

var TurnForm_manage = new manage();
TurnForm_manage.handleEverything();

//Object for management of a turns form
function manage(){


	//PUBLIC FUNCTION: addRecordToDropdownSelect
	//Add the created record, with the given modelName, uid and name to the
	//appropriate dropdown select box.
	this.addRecordToDropdownSelect = function( modelName, uid, name ){
		
		jQuery( '.modelRecordSelect[modelName="' + modelName + '"]' ).append(
			'<option value="' + uid + '" modelName="' + modelName + '" uid="' + uid + '" name="' + name + '">' +
			name + 
			'</option>'
		);
		jQuery( '.modelRecordSelect[modelName="' + modelName + '"]' ).val(
			uid
		);
		
	};

	//PUBLIC FUNCTION: handleEverything
	//This is the Pepper Potts (Iron Man reference, look it up) function
	//Basically it should just take care of everything so we can go on 
	//mad adventures elsewhere in the code
	this.handleEverything = function(){
	
		TurnForm_manage.handleNewRecordButton();
		TurnForm_manage.handleRecordSelection();
		
	};

	//PUBLIC FUNCTION: handleNewRecordButton
	//This function will be called when the page is setup and attaches
	//the event listener to all the new record buttons that'll run the
	//REST call to get the JSON needed to update the page
	this.handleNewRecordButton = function(){
		
		//Throw on the listener
		jQuery( '.addNewRecord' ).click( function(){
				
				//Get the controller name so that we're creating the right type 
				//of new record
				var controller = jQuery( this ).attr( 'controllerName' );
				
				//Make the necessary call
				jQuery.getJSON(
					homeURL + '/' + controller + '/newRecord', 
					function( data ){
						TurnForm_manage.newRecordButtonCallback(data);
					}
				).done( 
					function(){
					}
				).fail( 
					function(data){
					}
				).always(
					function(){
					}
				);
				
		});
		
	};
	
	//PUBLIC FUNCTION: handleRecordSelection
	//If a new record is selected from the drop down menu then we should pop
	//all of the selected records information into the text fields 
	this.handleRecordSelection = function(){
		
		jQuery( '.modelRecordSelect' ).change( function(){
			
			TurnForm_manage.loadRecordData( this );
			
		});
		
	};
	
	//PUBLIC FUNCTION: loadRecordData
	//Get the necessary information and make the call to grab all of record
	//data, then pass control to the populateRecordData function to make sure
	//it all actually gets displayed in the appropriate text boxes
	this.loadRecordData = function( element ){
		
		//Get the model name
		var modelName 	= jQuery(element).attr('modelName');
		
		//Get the appropriate controller
		var controller = jQuery(element).attr( 'controllerName' );
		
		//Get the UID we're working with
		var uid 		= jQuery(element).val();
		
		//Make a JSON request to get all the appropriate data
		jQuery.getJSON(
					homeURL + controller + '/getRecordData', 
					{
						uid:uid
					},
					function( data ){
						TurnForm_manage.populateRecordData(data);
					}
				).done( 
					function(){
					}
				).fail( 
					function(data){
					}
				).always(
					function(){
					}
				);
		
		
		
	};
	
	//PUBLIC FUNCTION: newRecordButtonCallback
	//This function will update the form with the data returned as
	//a result of the REST call to create a new record
	this.newRecordButtonCallback = function( data ){
	
		//Add the returned record to the dropdown select
		TurnForm_manage.addRecordToDropdownSelect( data['modelName'], data['uid'], data['name'] );
		
	};
	
	//PUBLIC FUNCTION: populateRecordData
	//Take loaded record data and throw it in all of the appropraite text boxes
	this.populateRecordData = function( json ){
	
		//Get the model name
		var modelName = json['modelName'];
		
		//Toss all the data into the closest appropriate receptical
		//Loop through every piece of the data
		jQuery.each( json, function( key, value ){
			jQuery( 'input.setupFormInputBox[modelName="' + modelName + '"][fieldName="' + key + '"]' ).val(
				value
			);
						
		});
		
		
		
	}
	
};