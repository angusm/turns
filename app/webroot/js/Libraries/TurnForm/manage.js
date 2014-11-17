//Object for management of a turns form
window.Turns.TurnFormManage = {};

window.Turns.TurnFormManage.prototype = {


	//PUBLIC FUNCTION: addRecordToDropdownSelects
	//Add the created record, with the given modelName, uid and name to the
	//appropriate dropdown select boxes.
	addRecordToDropdownSelects:function( modelName, uid, name ){

        var self = this;

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
            self.loadRecordData( this );

        });

        jQuery(
            'select' +
            '.associatedModelSelect' +
            '[controllername="'+pluralize(modelName)+'"]' +
            '[fieldname="'+tableize(modelName)+'_uid"]'
        ).append(
          '<option uid="'+uid+'" value="'+uid+'">' +
              name +
          '</option>'
        );

	},

	//PUBLIC FUNCTION: getFieldDataInArray
	//Get the field name and value from a given input field
	getFieldDataInArray:function( saveParameters, element ){

        var fieldName 	= jQuery( element ).attr( 'fieldName' );

        saveParameters[fieldName] = jQuery( element ).val();

        return saveParameters;

	},

	//PUBLIC FUNCTION: handleEverything
	//This is the Pepper Potts (Iron Man reference, look it up) function
	//Basically it should just take care of everything so we can go on
	//mad adventures elsewhere in the code
	handleEverything:function(){

		this.handleNewRecordButton();
        this.handleRecordSelection();
        this.handleSaveButton();
        this.loadSelections();

	},

	//PUBLIC FUNCTION: handleNewRecordButton
	//This function will be called when the page is setup and attaches
	//the event listener to all the new record buttons that'll run the
	//REST call to get the JSON needed to update the page
	handleNewRecordButton:function(){

        var self = this;

		//Throw on the listener
        jQuery(document).on(
            'click',
            '.addNewRecord',
            function(){

                //Get the controller name so that we're creating the right type
                //of new record
                var controller = jQuery( this ).attr( 'controllerName' );

                //Make sure the loading for this model is setup
                self.showLoadingForController( controller, true );

                //Make the necessary call
                jQuery.getJSON(
                    window.Paths.webroot + '/' + controller + '/newRecord',
                    function( data ){
                        self.newRecordButtonCallback(data);
                    }
                );

            }
        );

	},

	//PUBLIC FUNCTION: handleRecordSelection
	//If a new record is selected from the drop down menu then we should pop
	//all of the selected records information into the text fields
	handleRecordSelection:function(){

        //Preserve context
        var self = this;

        //Add the handler
        jQuery(document).on(
            'change',
            '.modelRecordSelect',
            function(){
                self.loadRecordData(this);
            }
        );

	},

	//PUBLIC FUNCTION: handleSaveButton
	//Handle committing entered/selected data to the database wehn the
	//Save Buttons are clicked
	handleSaveButton:function(){

        var self = this;
        jQuery( document).on(
            'click',
            '.saveRecord',
            function(){
                self.saveData(this)
            }
        );

	},

	//PUBLIC FUNCTION: loadRecordData
	//Get the necessary information and make the call to grab all of record
	//data, then pass control to the populateRecordData function to make sure
	//it all actually gets displayed in the appropriate text boxes
	loadRecordData:function( element ){

		//Get the model name
		var modelName 	= jQuery(element).attr('modelName');

		//Get the appropriate controller
		var controller = jQuery(element).attr('controllerName');

		//Get the UID we're working with
		var uid 		= jQuery(element).val();

        //Set the loading
        this.showLoadingForController( controller, true );
		var self = this;

		//Make a JSON request to get all the appropriate data
		jQuery.getJSON(
            window.Paths.webroot + controller + '/getRecordData',
            {
                uid:uid
            },
            function( data ){
                self.populateRecordData(data);
                self.showLoadingForController( controller, false );
            }
        );



	},

	//PUBLIC FUNCTION: loadSelections
	//Load the starting selections into the input fields
	loadSelections:function(){

        var self = this;
		//Load all the record data into all the input fields
		jQuery( '.modelRecordSelect' ).each( function(){
            self.loadRecordData( this );
		});


	},

	//PUBLIC FUNCTION: newRecordButtonCallback
	//This function will update the form with the data returned as
	//a result of the REST call to create a new record
	newRecordButtonCallback:function( data ){

		//Add the returned record to the dropdown select
		this.addRecordToDropdownSelects(
            data['modelName'],
            data['uid'],
            data['name']
        );

	},

	//PUBLIC FUNCTION: populateRecordData
	//Take loaded record data and throw it in all of the appropraite text boxes
	populateRecordData:function( json ){

		//Toss all the data into the closest appropriate receptacle
		//Loop through every piece of the data
		jQuery.each( json, function( modelName, fields ){

			//Grab the model UID
            var uid = fields['uid'];
            jQuery.each( fields, function(key, value){
                jQuery('input.setupFormInputBox[modelName="'+modelName+'"][fieldName="'+key+'"]').val(
                    value
                );
                jQuery('select[modelName="'+modelName+'"][fieldName="'+key+'"]').val(
                    value
                );

                //Update the selector with the name if we can
                if( 'name' == key ){
                    jQuery(
                        'select' +
                            '.associatedModelSelect' +
                            '[controllername="'+pluralize(modelName)+'"]' +
                            '[fieldname="'+tableize(modelName)+'_uid"]' +
                            ' > ' +
                            'option' +
                            '[uid="'+uid+'"]'
                    ).html(value);

                    jQuery(
                        'select' +
                        '.modelRecordSelect' +
                        '[modelname="'+modelName+'"]' +
                        ' > ' +
                        'option' +
                        '[uid="'+uid+'"]'
                    ).each( function(){

                        //Change the name in the attribute and html
                        jQuery(this).attr( 'name', value );
                        jQuery(this).html( value );

                    });

                }
            });
		});

	},

	//PUBLIC FUNCTION: saveData
	saveData:function( element ){

        //Preserve context
        var self = this;

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
			saveParameters = self.getFieldDataInArray( saveParameters, this );
		});
		jQuery( '.associatedModelSelect[modelName="' + modelName + '"]' ).each( function(){
			saveParameters = self.getFieldDataInArray( saveParameters, this );
		});
        this.showLoadingForController( controller, true );

		//Make a JSON request to get all the appropriate data
		jQuery.getJSON(
            window.Paths.webroot + controller + '/saveFormData',
            saveParameters,
            function( data ){
                //Load the record data into the fields

                self.populateRecordData(data);
                self.showLoadingForController( controller, false );

                /*jQuery( '.modelRecordSelect[modelName="' + modelName + '"]' ).each( function(){
                    self.loadRecordData( this );
                });*/
            }
        );

	},

    //PUBLIC FUNCTION: showLoadingForController
    //Show the loading div for the given controller's model
    showLoadingForController:function( controllerName, status ){

        if( true != status && false != status ){
            status = true;
        }
        if( status ){
            jQuery('div.loadingDiv[controllerName="'+controllerName+'"]').removeClass('hidden');
        }else{
            jQuery('div.loadingDiv[controllerName="'+controllerName+'"]').addClass('hidden');
        }

    }

};


jQuery( document).ready( function(){
    var TurnForm_manage = new TurnFormManage();
    TurnForm_manage.handleEverything();
});