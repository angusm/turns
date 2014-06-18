//PEPPER POTTS FUNCTION: handleEverything
//Why Pepper Potts? Cause she's awesome.

var TurnForm_manage = new manage();
TurnForm_manage.handleEverything();

//Object for management of a turns form
function manage(){


	//PUBLIC FUNCTION: addRecordToDropdownSelect
	//Add the created record, with the given modelName, uid and name to the
	//appropriate dropdown select box.
	this.addRecordToDropdownSelect = function( modelName, uid, name ){
		
		jQuery( '.modelRecordSelect[modelName="' + modelName + '"]' ).each( function(){

            //Add the new record to the relevant drop downs
            jQuery(this).append(
                '<option value="' + uid + '" modelName="' + modelName + '" uid="' + uid + '" name="' + name + '">' +
                    name +
                    '</option>'
            );
		
            //Set the new record as the selected record
            jQuery(this).val(
                uid
            );

            //Load the record data into the fields
            TurnForm_manage.loadRecordData( this );

        });
		
	};
	
	//PUBLIC FUNCTION: getFieldDataInArray
	//Get the field name and value from a given input field
	this.getFieldDataInArray = function( saveParameters, element ){
			
			var fieldName 	= jQuery( element ).attr( 'fieldName' );
			var fieldValue	= jQuery( element ).val();
			saveParameters[fieldName] = fieldValue;
			
			return saveParameters;
			
	};

	//PUBLIC FUNCTION: handleEverything
	//This is the Pepper Potts (Iron Man reference, look it up) function
	//Basically it should just take care of everything so we can go on 
	//mad adventures elsewhere in the code
	this.handleEverything = function(){
	
		TurnForm_manage.handleNewRecordButton();
		TurnForm_manage.handleRecordSelection();
		TurnForm_manage.handleSaveButton();
		TurnForm_manage.loadSelections();
		
	};

	//PUBLIC FUNCTION: handleNewRecordButton
	//This function will be called when the page is setup and attaches
	//the event listener to all the new record buttons that'll run the
	//REST call to get the JSON needed to update the page
	this.handleNewRecordButton = function(){

        var _this = this;

		//Throw on the listener
		jQuery( '.addNewRecord' ).click( function(){

				//Get the controller name so that we're creating the right type 
				//of new record
				var controller = jQuery( this ).attr( 'controllerName' );

                //Make sure the loading for this model is setup
                _this.showLoadingForController( controller, true );

				//Make the necessary call
				jQuery.getJSON(
					homeURL + '/' + controller + '/newRecord', 
					function( data ){
						TurnForm_manage.newRecordButtonCallback(data);
                        _this.showLoadingForController( controller, false );
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
	
	//PUBLIC FUNCTION: handleSaveButton
	//Handle committing entered/selected data to the database wehn the
	//Save Buttons are clicked
	this.handleSaveButton = function(){
		
		//Setup the event listener for the click
		jQuery( '.saveRecord' ).click( function(){
			
			//Save the data to the database
			TurnForm_manage.saveData( this );

		});
		
	}
	
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

        //Set the loading
        this.showLoadingForController( controller, true );
		var _this = this;

		//Make a JSON request to get all the appropriate data
		jQuery.getJSON(
            homeURL + controller + '/getRecordData',
            {
                uid:uid
            },
            function( data ){
                TurnForm_manage.populateRecordData(data);
                _this.showLoadingForController( controller, false );
            }
        );
		
		
		
	};
	
	//PUBLIC FUNCTION: loadSelections
	//Load the starting selections into the input fields
	this.loadSelections = function( data ){

		//Load all the record data into all the input fields
		jQuery( '.modelRecordSelect' ).each( function(){
			TurnForm_manage.loadRecordData( this );
		});
			
		
	}
	
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
		
		//Toss all the data into the closest appropriate receptacle
		//Loop through every piece of the data
		jQuery.each( json, function( key, value ){
			jQuery('input.setupFormInputBox[modelName="'+modelName+'"][fieldName="'+key+'"]').val(
				value
			);
            jQuery('select[modelName="'+modelName+'"][fieldName="'+key+'"]').val(
                value
            );
		});
		
	};
	
	//PUBLIC FUNCTION: saveData
	this.saveData = function( element ){
		
		//Get the model name that we're saving
		var modelName = jQuery( element ).attr('modelname');
		
		//Get the appropriate controller
		var controller = jQuery(element).attr( 'controllerName' );
		
		//Setup an array to contain the jSon stuff we'll be pasing to the
		//CakePHP Controller we call in order to save this information
		var saveParameters = {};
		
		//Gather all the information relevant to this model and save it
		//in an array that we can then pass as jSon values
		jQuery( '.setupFormInputBox[modelName="' + modelName + '"]' ).each( function(){
			saveParameters = TurnForm_manage.getFieldDataInArray( saveParameters, this );
		});
		jQuery( '.associatedModelSelect[modelName="' + modelName + '"]' ).each( function(){
			saveParameters = TurnForm_manage.getFieldDataInArray( saveParameters, this );
		});

        this.showLoadingForController( controller, true );
        var _this = this;
                
		//Make a JSON request to get all the appropriate data
		jQuery.getJSON(
            homeURL + controller + '/saveFormData',
            saveParameters,
            function( data ){

                //Load the record data into the fields
                jQuery( '.modelRecordSelect[modelName="' + modelName + '"]' ).each( function(){
                    TurnForm_manage.loadRecordData( this );
                });
                _this.showLoadingForController( controller, false );

            }
        );
		
	};

    //PUBLIC FUNCTION: showLoadingForController
    //Show the loading div for the given controller's model
    this.showLoadingForController = function( controllerName, status ){

        if( status != true && status != false ){
            status = true;
        }

        if( status ){
            jQuery('div.loadingDiv[controllerName="'+controllerName+'"]').removeClass('hidden');
        }else{
            jQuery('div.loadingDiv[controllerName="'+controllerName+'"]').addClass('hidden');
        }

    }
	
};