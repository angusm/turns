<?php

//This class will be used to make badass forms just the way I like them

App::uses('AppHelper', 'View/Helper');

/**
 * Class TurnFormHelper
 */
class TurnFormHelper extends AppHelper {
	
	//We'll be using some of the HTML helper's functionality to do awesome stuff
  	var $helpers = ['Html'];

	
	//PUBLIC FUNCTION: editableModelSelect
	//OPTIONAL PARAMETERS:
	//	displayField => 		The field name of the model whose value should be shown to the user
	//	extraAttributes =>		Array of extra attributes to be attached to the select
	//	includeSaveButton =>	Boolean indicating whether or not a save button should be shown
	//Create a standard model select with an overlayed textbox input so that it can be edited
	/**
	 * @param $modelArray
	 * @param array $optionalParameters
	 * @return string
	 */
	public function editableModelSelect( $modelArray, $optionalParameters=[] ){
	
	
		//Assign and then, if necessary replace default optionals
		$displayField			= 'name';
		$extraAttributes 		= [];
		$includeSaveButton		= false;
		$includeRemoveButton	= false;
		$includeNewButton		= false;
		
		if( isset( $optionalParameters['displayField'] ) ){
			$displayField = $optionalParameters['displayField'];
		}
		if( isset( $optionalParameters['extraAttributes'] ) ){
			$extraAttributes = $optionalParameters['extraAttributes'];
		}
		if( isset( $optionalParameters['includeNewButton'] ) ){
			$includeNewButton = $optionalParameters['includeNewButton'];
		}
		if( isset( $optionalParameters['includeRemoveButton'] ) ){
			$includeRemoveButton = $optionalParameters['includeRemoveButton'];	
		}
		if( isset( $optionalParameters['includeSaveButton'] ) ){
			$includeSaveButton = $optionalParameters['includeSaveButton'];
		}
		
		///First things first, we check to see if the modelArray
		//we were given is in fact an array. If not we assume that
		//it's a model name and carry on about our business
		if( ! is_array( $modelArray ) ){
			$modelName 			= $modelArray;
			$usableModel 		= ClassRegistry::init( $modelName );
			$modelArray 		= $usableModel->getManagementList();
			
		//Do a quick check to see if we have nothing to display, if we got nothing
		//then setup a blank
		}else if( count( $modelArray ) == 0 ){
			
			$modelName = 'Unknown';
			
		}else{
			//First we reset our array, just in case something weird happened to it
			reset( $modelArray[0] );
			
			//Now when we grab the first key of this array it'll give us the model we're working with
			$modelName 			= key( $modelArray[0] );
		}
		
		//Create a new DateTime instance so we can get a unique ID for the editable select
		$dateTimeInstance = new DateTime();
		
		//We set the class to an editableModelSelect and add an editableModelSelect attribute
		$baseAttributes = [
							'class' 			=> 'editableSelect',
							'editableSelect'	=> $dateTimeInstance->getTimeStamp()
						];
						
		
		//Merge in any extra attributes we have to work with
		$attributes = array_merge( $baseAttributes, $extraAttributes );
		
		
		//Setup the return string
		$returnString = '';
		
		//Add a regular model select
		$returnString .= $this->modelSelect( $modelArray, $displayField, $attributes );
		
		//Setup a text box to add alongside
		$returnString .= $this->fieldInputTextBox( $modelName, $displayField, $attributes );
		
		//Include a save button if requested
		if( $includeSaveButton ){
		
			$returnString .= $this->saveRecordButton( 
													  $modelName, 
													  array_merge(
														  $baseAttributes,
														  [
															'class' => 'editableSelectSave'
														  ]
													  )
												  );	
			
		}
		
		//Add a remove record button if requested
		if( $includeRemoveButton ){
			
			$returnString .= $this->removeRecordButton( 
													  $modelName, 
													  array_merge(
														  $baseAttributes,
														  [
															'class' => 'editableSelectRemove'
														  ]
													  )
												  );	

		}
		
		//Add a new record button if requested
		if( $includeNewButton ){
			
			$returnString .= $this->newRecordButton(
													$modelName,
													array_merge(
														$baseAttributes,
														[
															'class' => 'editableSelectNew'
														]
													)
												);
												
		}
		
		return $returnString;
		
		
	}
	
	//PUBLIC FUNCTION: fieldInput
	//Creates a labeled field for inputting data for a table row
	/**
	 * @param $modelName
	 * @param $fieldName
	 * @return string
	 */
	public function fieldInput( $modelName, $fieldName ){
		
		//Initialize the return string
		$returnString = '';
		
		//Give it a label
		$returnString .= $this->fieldInputLabel( $modelName, $fieldName );
		
		//Give it an input
		$returnString .= $this->fieldInputTextbox( $modelName, $fieldName );
		
		//Return the html data
		
		return $returnString;
											
	}
	
	//PUBLIC FUNCTION: fieldInputLabel
	//Return a nice label for the field
	/**
	 * @param $modelName
	 * @param $fieldName
	 * @return mixed
	 */
	public function fieldInputLabel( $modelName, $fieldName ){
		
		//Get some nice human type names for things	
		$humanFieldName = Inflector::humanize( $fieldName );

		return $this->Html->tag( 
								'label',
								$humanFieldName,
								[
									'for' 	=> $modelName . $fieldName,
									'class'		=> 'setupFormLabel',
									'modelName'	=> $modelName,
									'fieldName'	=> $fieldName	
								]
							);
		
	}
	
	//PUBLIC FUNCTION: fieldInputTextbox
	//Return a nice label for the field
	/**
	 * @param $modelName
	 * @param $fieldName
	 * @param array $extraAttributes
	 * @return mixed
	 */
	public function fieldInputTextbox( $modelName, $fieldName, $extraAttributes=[] ){
		
		//Get some nice human type names for things	
		$humanFieldName = Inflector::humanize( $fieldName );
		$humanModelName = Inflector::humanize( $modelName );
		
		//Setup the attributes array
		$attributes = [
							'type'		=> 'text',
							'class'		=> 'setupFormInputBox',
							'name'		=> $humanModelName . ' . ' . $humanFieldName,
							'modelName'	=> $modelName,
							'fieldName'	=> $fieldName
						];
		
		//Merge in any extra attributes we have to work with
		$attributes = array_merge( $attributes, $extraAttributes );
		
		return $this->Html->tag( 
								  'input',
								  '',
								  $attributes
							  );
		
	}
	
	//PUBLIC FUNCTION: fieldsFromInitialModelStructure
	//Return the fields from an initial model structure
	/**
	 * @param $modelName
	 * @param $structure
	 * @return string
	 */
	public function fieldsFromModelStructure( $modelName, $structure ){
		
		//Initialize the return string
		$returnString = '';
										
		//Add the select and save / new buttons to the display
		$returnString .=  $this->modelSelect( $modelName );
		$returnString .=  $this->newRecordButton( $modelName );
		$returnString .=  $this->saveRecordButton( $modelName );
        $returnString .=  $this->removeRecordButton( $modelName );
				
		//List the fields for the initial model
		foreach( $structure as $fieldName ){
			$returnString .= $this->fieldInput( $modelName, $fieldName ) . '<BR>';
		}
		
		//Return the return string
		return $returnString;		
		
	}
		
	//PUBLIC FUNCTION: fullModelSetupForm
	//Returns a complete form with all the necessary fields to completely
	//set up a model.
	/**
	 * @param $structure
	 * @param string $extraContent
	 * @return string
	 */
	public function fullModelSetupForm( $structure, $extraContent='' ){
	
		//Initialize the return string
		$returnString 			= '';
		$returnStringContent	= '';
		$clearDiv 				= $this->Html->tag(
												'div',
												'',
												[
													'class' => 'clearDiv'
												]
											);
		
		//Loop through each model name in the structure, this
		//should loop only once
		foreach( $structure as $modelName => $innerStructure ){
			
			//Get the humanized model name
			$humanModelName = Inflector::humanize( 
								Inflector::tableize( 
									$modelName 
								)
							);
			
			//Add a header
			$returnString .= $this->Html->tag( 
											'h3',
											$humanModelName,
											[
												'class'				=> 'modelSetupHeader disclosureToggle',
												'disclosureName'	=> $modelName . 'SetupForm'
											]
										);
	 
													
			//Get the fields for this model and throw it on
			$returnStringContent .= $this->tableFieldsFromModelStructure( $modelName, $structure[ $modelName ][ 'fields' ] );
			
			//Now that we've gotten our main model we loop through and start grabbing all
			//the form goodies for our inner models
			
			//We start with the hasOne relationship because it's the simplest,
			//we just do a recursive call to this same function
			foreach( $structure[ $modelName ]['hasOne']	as $associatedModelName => $associatedStructure ){
				$returnStringContent .= $this->fullModelSetupForm( [ $associatedModelName => $associatedStructure ] );
			}
			
			//The belongsTo relationship is similarly easy
			foreach( $structure[ $modelName ]['belongsTo'] 	as $associatedModelName => $associatedStructure ){
				$returnStringContent .= $this->fullModelSetupForm( [ $associatedModelName => $associatedStructure ] );
			}
			
			//Now we move onto the hasMany relationships
			foreach( $structure[ $modelName ]['hasMany']	as $associatedModelName => $associatedStructure ){
				$returnStringContent .= $this->fullModelSetupForm( [ $associatedModelName => $associatedStructure ] );
			}

            //Throw all this lovely content inside of a disclosure div so that it displays nicely
            $returnString .= $this->Html->tag(
                'div',
                $extraContent . $returnStringContent . $clearDiv,
                [
                    'class'				=> 'disclosureDiv',
                    'disclosureName'	=> $modelName . 'SetupForm'
                ]
            );
			
		}
		
		//We want to be able to indent included associations, so for this purpose we add a nice little padding spacer
		$returnString = $this->Html->tag(
										'div',
										$returnString,
										[
											'class'	=> 'modelSetupFormSpacerDiv'
										]
									);
		
		//Eventually we have to return the return string
		return $returnString;
		
	}

    //PUBLIC FUNCTION: loadingDiv
    //A div that can be displayed (is hidden by default) when requests to the server
    //are in progress
	/**
	 * @param $modelName
	 * @param array $extraAttributes
	 * @return mixed
	 */
	public function loadingDiv($modelName, $extraAttributes=[] ){

        //Get the internal name, i.e. strip out the spaces
        $internalModelName 	= str_replace(' ', '', $modelName);
        $controllerName		= Inflector::pluralize( $internalModelName );

        //Add the attributes
        $attributes = [
            'class'			    => 	'loadingDiv hidden',
            'controllerName'	=> 	$controllerName,
            'modelName'		    =>	$internalModelName,
        ];

        //Add any extra requeseted attributes
        $attributes = array_merge( $attributes, $extraAttributes );

        //Return the properly formatted tag
        return $this->Html->tag(
            'div',
            'Loading...',
            $attributes
        );
    }
		
	//PUBLIC FUNCTION: modelSelect
	//Creates a select box that contains all the data of the immediate
	//model abstracted into the nice little metadata of the select boxes
	/**
	 * @param $modelArray
	 * @param string $displayField
	 * @param array $extraAttributes
	 * @return mixed
	 */
	public function modelSelect( $modelArray, $displayField='name', $extraAttributes=[] ){
	
		//First things first, we check to see if the modelArray
		//we were given is in fact an array. If not we assume that
		//it's a model name and carry on about our business
		if( ! is_array( $modelArray ) ){
			
			$modelName 			= $modelArray;
			$usableModel 		= ClassRegistry::init( $modelName );
			$modelArray 		= $usableModel->getManagementList();
			
		//Do a quick check to see if we have nothing to display, if we got nothing
		//then setup a blank
		}else if( count( $modelArray ) == 0 ){
			
			$modelName = 'Unknown';
			
		}else{
			
			//First we reset our array, just in case something weird happened to it
			reset( $modelArray[0] );
			
			//Now when we grab the first key of this array it'll give us the model we're working with
			$modelName 			= key( $modelArray[0] );
		}
		
		//Grab the controller name		
		$controllerName 	= Inflector::pluralize( $modelName );
		
		//Setup whatever containers we'll be needing.
		$attributes		= [];
		$optionsString 	= '';
	
		
		//Now that we've gotten what we want from the main model we can set the attributes for
		//our select.
		$attributes['modelName'] 		= $modelName;	
		
		//Throw on the controller name
		$attributes['controllerName'] 	= $controllerName;
		
		//Define the select with a class that states what it is, a select box for picking model
		//records
		$attributes['class']	 		= 'modelRecordSelect';	
		
		//Merge in any extra attributes we have to work with
		$attributes = array_merge( $attributes, $extraAttributes );
		
	
		//Loop through each iteration of the model array
		foreach( $modelArray as $modelInstance ){
			
			//We grab the display value if its set (defaults to name) 
			//otherwise we default to the UID
			//If for some reason we have a model without a UID, well then FUCK
			//Somebody dun' fucked up the database. We need semantically meaningless
			//and purely synthetic UID keys.
			if( isset( $modelInstance[$modelName][$displayField] ) ){
				//Do fuck all.
			}else{
				$displayField = 'uid';
			}
			
			//Make sure we add a value to the option so that we can use all kinds of lovely
			//value based jQuery on it
			$modelInstance[$modelName]['value'] = $modelInstance[$modelName]['uid'];
			
			//Add this option with all its sexy meta data to the options string
			$optionsString .= $this->Html->tag( 
				'option', 
				$modelInstance[$modelName][$displayField], 
				$modelInstance[$modelName] 
			);			
			
		}
		
		
		//Now that we've defined all our options we just nest them inside of a select
		//and we're off to the raises.
		$returnString = $this->Html->tag( 'select', $optionsString, $attributes );
		
		return $returnString;
		
	}
	
	//PUBLIC FUNCTION: newRecordButton
	//A button designed for creating new records of models
	//The model name can be provided with spaces, so that if for
	//instance we were to create a newRecord button for UnitType
	//We could safely provide the model name as "Unit Type"
	/**
	 * @param $modelName
	 * @param array $extraAttributes
	 * @return mixed
	 */
	public function newRecordButton( $modelName, $extraAttributes=[] ){
	
		//Get the internal name, i.e. strip out the spaces
		$internalModelName 	= str_replace(' ', '', $modelName);
		$controllerName		= Inflector::pluralize( $internalModelName );
	
		$baseAttributes = [
								'type'				=> 	'button',
								'class'				=> 	'addNewRecord',
								'controllerName'	=>	$controllerName,
								'modelName'			=>	$internalModelName,
								'value'				=> 	'New '.$modelName
							];
	
		//Return the properly formatted tag
		return $this->Html->tag(	
							'input',
							'',
							array_merge(
								$baseAttributes,
								$extraAttributes
							)
						);
		
	}
		
	//PUBLIC FUNCTION: removeRecordButton
	//A button designed for removing records, just feed it
	//a model name same as you would for a newRecordButton
	/**
	 * @param $modelName
	 * @param array $extraAttributes
	 * @return mixed
	 */
	public function removeRecordButton( $modelName, $extraAttributes=[] ){
	
		//Get the internal name, i.e. strip out the spaces
		$internalModelName 	= str_replace(' ', '', $modelName);
		$controllerName		= Inflector::pluralize( $internalModelName );
	
		//Add the attributes
		$attributes = [
							'type'				=> 	'button',
							'class'				=> 	'removeRecord',
							'controllerName'	=> 	$controllerName,
							'modelName'			=>	$internalModelName,
							'value'				=> 	'Delete '.$modelName
						];
							
		//Add any extra requested attributes
		$attributes = array_merge( $attributes, $extraAttributes );
	
		//Return the properly formatted tag
		return $this->Html->tag(	
							'input',
							'',
							$attributes
						);	
		
	}
		
	//PUBLIC FUNCTION: saveRecordButton
	//A button designed for saving records, just feed it
	//a model name same as you would for a newRecordButton
	/**
	 * @param $modelName
	 * @param array $extraAttributes
	 * @return mixed
	 */
	public function saveRecordButton( $modelName, $extraAttributes=[] ){
	
		//Get the internal name, i.e. strip out the spaces
		$internalModelName 	= str_replace(' ', '', $modelName);
		$controllerName		= Inflector::pluralize( $internalModelName );
	
		//Add the attributes
		$attributes = [
							'type'			=> 	'button',
							'class'			=> 	'saveRecord',
							'controllerName'	=> 	$controllerName,
							'modelName'		=>	$internalModelName,
							'value'			=> 	'Save '.$modelName
						];
							
		//Add any extra requested attributes
		$attributes = array_merge( $attributes, $extraAttributes );
	
		//Return the properly formatted tag
		return $this->Html->tag(	
							'input',
							'',
							$attributes
						);	
		
	}
	
	//PUBLIC FUNCTION: tableFieldInput
	//Creates a labeled field for inputting data for a SQL table row
	//inside of tags that will fit nicely inside of an HTML table
	//All kinds of tables up in this bitch
	/**
	 * @param $parentModelName
	 * @param $modelName
	 * @param $fieldName
	 * @return mixed
	 */
	public function tableFieldBelongsToSelection( $parentModelName, $modelName, $fieldName ){

		//Give it a label
		$labelString = $this->fieldInputLabel( $parentModelName, $fieldName );
		
		//Give it an input 
		$inputString = $this->modelSelect( 
										$modelName, 
										'name', 
										[
											'class' 	=> 'associatedModelSelect',
											'fieldName'	=> $fieldName,
											'modelName' => $parentModelName
										]
									);
		
		//Create a table row containing the two strings.
		$labelString = $this->Html->tag(
										'td',
										$labelString,
										[
											'class' => 'setupFormLabel'
										]
									);
		$inputString = $this->Html->tag(
										'td',
										$inputString,
										[
											'class' => 'setupFormInput'
										]
									);
									
		$returnString = $this->Html->tag(
										'tr',
										$labelString . $inputString,
										[]
									);

									
									
		//Return the html data
		return $returnString;
											
	}
	
	//PUBLIC FUNCTION: tableFieldInput
	//Creates a labeled field for inputting data for a SQL table row
	//inside of tags that will fit nicely inside of an HTML table
	//All kinds of tables up in this bitch
	/**
	 * @param $modelName
	 * @param $fieldName
	 * @return string
	 */
	public function tableFieldInput( $modelName, $fieldName ){
			
		if(
            $fieldName != 'created' &&
            $fieldName != 'modified'
        ){
            //Give it a label
            $labelString = $this->fieldInputLabel( $modelName, $fieldName );

            //Give it an input
            $inputString = $this->fieldInputTextbox( $modelName, $fieldName );

            //Create a table row containing the two strings.
            $labelString = $this->Html->tag(
                                            'td',
                                            $labelString,
                                            [
                                                'class' => 'setupFormLabel'
                                            ]
                                        );
            $inputString = $this->Html->tag(
                                            'td',
                                            $inputString,
                                            [
                                                'class' => 'setupFormInput'
                                            ]
                                        );

            $returnString = $this->Html->tag(
                                            'tr',
                                            $labelString . $inputString,
                                            []
                                        );
        }else{
            $returnString = '';
        }

									
									
		//Return the html data
		return $returnString;
											
	}
	
	//PUBLIC FUNCTION: tableFieldsFromInitialModelStructure
	//Return the fields from an initial model structure
	/**
	 * @param $modelName
	 * @param $structure
	 * @return mixed
	 */
	public function tableFieldsFromModelStructure( $modelName, $structure ){
				
		//Setup a string to hold all the lovely contents
		//that we'll be unceremoniously cramming inside the table
		$tableContents = '';
			
		//We create an instance of the model name so that we can grab the 
		//belongsTo entry for the model and create an array of the various
		//fields and models that are contained in it.
		//
		//In this way when we create the fields we can create a model select
		//field instead of an input field
		$modelInstance = ClassRegistry::init( $modelName );

		//Setup an array to contain the fields
		$belongsToFields = $modelInstance->getBelongsToFieldsArray();
										
		//Add the select and save / new buttons to the display
		$tableContents .=   $this->modelSelect( $modelName );
		$tableContents .=   $this->newRecordButton( $modelName );
        $tableContents .=   $this->saveRecordButton( $modelName );
        $tableContents .=   $this->removeRecordButton( $modelName );
        $tableContents .=   $this->loadingDiv( $modelName );
		
		//List the fields for the initial model
		foreach( $structure as $fieldName ){
			
			//If the given field is a belongsTo field then create a model select
			if( isset( $belongsToFields[$fieldName] ) ){
				$tableContents .= $this->tableFieldBelongsToSelection( $modelName, $belongsToFields[$fieldName], $fieldName );
				
			//If the given field is just a field, then show it as an input field
			}else{
				$tableContents .= $this->tableFieldInput( $modelName, $fieldName );
			}
			
			
		}
		
		//Create the table tag and unceremoniously cram in
		//the contents
		$returnString = $this->Html->tag(
			'table',
			$tableContents,
			[
				'class' => 'modelFields'
			]
		);
									
		//Return the table inside of a div so that it will display
		//nicely
		$returnString = $this->Html->tag(
			'div',
			$returnString,
			[
				'class' => 'modelFieldsDiv'
			]
		);
		
		//Return the return string
		return $returnString;		
		
	}
	
}