<?php

//This class will be used to make badass forms just the way I like them

App::uses('AppHelper', 'View/Helper');

class TurnFormHelper extends AppHelper {
	
	//We'll be using some of the HTML helper's functionality to do awesome stuff
  	var $helpers = array('Html');
	
	
	//PUBLIC FUNCTION: associationPicker
	//Creates a scrollable div with buttons that can be used to
	//toggle associations with a given model
	public function associationPicker( $modelName ){
	
		//Initialize the return strings
		$returnString 	= '';
		$divContents	= '';
		
		//Initialize the appropriate model
		$usableModel = ClassRegistry::init( $modelName );
		
		//Get the management list for the model
		$managementList = $usableModel->getManagementList();
		
		//Loop through the management list and for each one
		foreach( $managementList as $associatedModel ){
			
			//Check to see if the model has a name
			//If it doesn't then set the display name to its UID
			if( isset( $associatedModel[$modelName]['name'] ) ){
				$displayName = $associatedModel[$modelName]['name'];
			}else{
				$displayName = $associatedModel[$modelName]['uid'];
			}
			
			//Create a button to allow associations to be
			//toggled
			$divContents .= $this->Html->tag(
											'input',
											'',
											array(
												'class'				=> 'toggleAssociation disclosureDiv',
												'disclosureName'	=> $modelName . 'AssociationPicker',
												'modelName'			=> $modelName,
												'state'				=> 'unselected',
												'type'				=> 'button',
												'uid'				=> $associatedModel[$modelName]['uid'],
												'value'				=> $displayName						
											)
										);
			
		}
		
		//Throw all those buttons inside of the picker and then
		//add the picker to the return string
		$returnString .= $this->Html->tag(
										'div',
										$divContents,
										array(
											'class'		=> 'associationPicker',
											'modelName'	=> $modelName
										)
									);
									
		//If there was ever something to do with a returnString it would
		//be to return it, so return it
		return $returnString;
		
	}
	
	//PUBLIC FUNCTION: fieldInput
	//Creates a labeled field for inputting data for a table row
	public function fieldInput( $modelName, $fieldName ){
	
		//Get some nice human type names for things	
		$humanFieldName = Inflector::humanize( $fieldName );
		$humanModelName = Inflector::humanize( $modelName );
		
		//Initialize the return string
		$returnString = '';
		
		//Give it a label
		$returnString .= $this->Html->tag( 
										'label',
										$humanModelName . ' . ' . $humanFieldName,
										array(
											'for' 	=> $modelName . $fieldName			
										)
									);
		//Give it an input
		$returnString .= $this->Html->tag(
										'input',
										'',
										array(
											'type'	=> 'text',
											'name'	=> $humanModelName . ' . ' . $humanFieldName,
											'id'	=> $modelName . $fieldName
										)
									);
									
		//Return the html data
		return $returnString;
											
	}
	
	//PUBLIC FUNCTION: fieldsFromInitialModelStructure
	//Return the fields from an initial model structure
	public function fieldsFromModelStructure( $modelName, $structure ){
		
		//Initialize the return string
		$returnString = '';
										
		//Add the select and save / new buttons to the display
		$returnString .=  $this->modelSelect( $modelName );
		$returnString .=  $this->newRecordButton( $modelName );
		$returnString .=  $this->saveRecordButton( $modelName );
				
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
	public function fullModelSetupForm( $structure, $extraContent='' ){
	
		//Initialize the return string
		$recordSelectors		= '';
		$returnString 			= '';
		$returnStringContent	= '';
		$clearDiv 		= $this->Html->tag(
										'div',
										'',
										array(
											'class' => 'clearDiv'
										)
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
											array(
												'class'				=> 'modelSetupHeader disclosureToggle',
												'disclosureName'	=> $modelName . 'SetupForm'
											)
										);
	 
													
			//Get the fields for this model and throw it on
			$returnStringContent .= $this->tableFieldsFromModelStructure( $modelName, $structure[ $modelName ][ 'Fields' ] );
			
			//Now that we've gotten our main model we loop through and start grabbing all
			//the form goodies for our inner models
			
			//We start with the hasOne relationship because it's the simplest,
			//we just do a recursive call to this same function
			foreach( $structure[ $modelName ]['hasOne']	as $associatedModelName => $associatedStructure ){
			
				$returnStringContent .= $this->fullModelSetupForm( array( $associatedModelName => $associatedStructure ) );
				
			}
			
			//The belongsTo relationship is similarly easy
			foreach( $structure[ $modelName ]['belongsTo'] 	as $associatedModelName => $associatedStructure ){
				
				$associationPicker 	  = $this->associationPicker( $associatedModelName );
				$returnStringContent .= $this->fullModelSetupForm( array( $associatedModelName => $associatedStructure ), $associationPicker );
					
			}
			
			//Now we move onto the hasMany relationships
			foreach( $structure[ $modelName ]['hasMany']	as $associatedModelName => $associatedStructure ){
			
				$associationPicker 		= $this->associationPicker( $associatedModelName );
				$returnStringContent   .= $this->fullModelSetupForm( array( $associatedModelName => $associatedStructure ), $associationPicker );
				
			}
			
		}
		
		//Throw all this lovely content inside of a disclosure div so that it displays nicely
		$returnString .= $this->Html->tag(
										'div',
										$extraContent . $returnStringContent . $clearDiv,
										array(
											'class'				=> 'disclosureDiv',
											'disclosureName'	=> $modelName . 'SetupForm'
										)
									);
		
		//We want to be able to indent included associations, so for this purpose we add a nice little padding spacer
		$returnString = $this->Html->tag(
										'div',
										$returnString,
										array(
											'class'	=> 'modelSetupFormSpacerDiv'
										)
									);
		
		//Eventually we have to return the return string
		return $returnString;
		
	}
		
	//PUBLIC FUNCTION: modelSelect
	//Creates a select box that contains all the data of the immediate
	//model abstracted into the nice little metadata of the select boxes
	public function modelSelect( $modelArray, $displayField='name' ){
	
		//First things first, we check to see if the modelArray
		//we were given is in fact an array. If not we assume that
		//it's a model name and carry on about our business
		if( ! is_array( $modelArray ) ){
			$modelName = $modelArray;
			$usableModel = ClassRegistry::init( $modelName );
			$modelArray = $usableModel->getManagementList();
		}else{
			//First we reset our array, just in case something weird happened to it
			reset( $modelArray[0] );
			
			//Now when we grab the first key of this array it'll give us the model we're working with
			$modelName = key( $modelArray[0] );
		}
		
		//Setup whatever containers we'll be needing.
		$attributes		= array();
		$optionsArray 	= array();
		$optionsString 	= '';
	
		
		//Now that we've gotten what we want from the main model we can set the attributes for
		//our select.
		$attributes['modelName'] = $modelName;		
	
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
			
			//Add this option with all its smexy meta data to the options string
			$optionsString .= $this->Html->tag( 'option', $modelInstance[$modelName][$displayField], $modelInstance[$modelName] );			
			
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
	public function newRecordButton( $modelName ){
	
		//Get the internal name, i.e. strip out the spaces
		$internalModelName = str_replace(' ', '', $modelName);
	
		//Return the properly formatted tag
		return $this->Html->tag(	
							'input',
							'',
							array(
								'type'		=> 	'button',
								'class'		=> 	'addNewRecord',
								'modelName'	=>	$internalModelName,
								'value'		=> 	'New '.$modelName
							)
						);	
		
	}
		
	//PUBLIC FUNCTION: saveRecordButton
	//A button designed for saving records, just feed it
	//a model name same as you would for a newRecordButton
	public function saveRecordButton( $modelName ){
	
		//Get the internal name, i.e. strip out the spaces
		$internalModelName = str_replace(' ', '', $modelName);
	
		//Return the properly formatted tag
		return $this->Html->tag(	
							'input',
							'',
							array(
								'type'		=> 	'button',
								'class'		=> 	'saveRecord',
								'modelName'	=>	$internalModelName,
								'value'		=> 	'Save '.$modelName
							)
						);	
		
	}
	
	//PUBLIC FUNCTION: tableFieldInput
	//Creates a labeled field for inputting data for a SQL table row
	//inside of tags that will fit nicely inside of an HTML table
	//All kinds of tables up in this bitch
	public function tableFieldInput( $modelName, $fieldName ){
	
		//Get some nice human type names for things	
		$humanFieldName = Inflector::humanize( $fieldName );
		$humanModelName = Inflector::humanize( $modelName );
		
		//Initialize the return string
		$returnString = '';
		
		//Give it a label
		//$humanModelName . ' . ' . 
		$labelString = $this->Html->tag( 
										'label',
										$humanFieldName,
										array(
											'class' => 'setupFormLabel',
											'for' 	=> $modelName . $fieldName	
										)
									);
		//Give it an input
		$inputString = $this->Html->tag(
										'input',
										'',
										array(
											'class' => 'setupFormInputBox',
											'id'	=> $modelName . $fieldName,
											'name'	=> $humanModelName . ' . ' . $humanFieldName,
											'type'	=> 'text'
										)
									);
									
		//Create a table row containing the two strings.
		$labelString = $this->Html->tag(
										'td',
										$labelString,
										array(
											'class' => 'setupFormLabel'
										)
									);
		$inputString = $this->Html->tag(
										'td',
										$inputString,
										array(
											'class' => 'setupFormInput'
										)
									);
									
		$returnString = $this->Html->tag(
										'tr',
										$labelString . $inputString,
										array()
									);

									
									
		//Return the html data
		return $returnString;
											
	}
	
	//PUBLIC FUNCTION: tableFieldsFromInitialModelStructure
	//Return the fields from an initial model structure
	public function tableFieldsFromModelStructure( $modelName, $structure ){
				
		//Setup a string to hold all the lovely contents
		//that we'll be unceremoniously cramming inside the table
		$tableContents = '';
				
										
		//Add the select and save / new buttons to the display
		$tableContents .= $this->modelSelect( $modelName );
		$tableContents .=  $this->newRecordButton( $modelName );
		$tableContents .=  $this->saveRecordButton( $modelName );
		
		//List the fields for the initial model
		foreach( $structure as $fieldName ){
			
			$tableContents .= $this->tableFieldInput( $modelName, $fieldName );
			
		}
		
		//Create the table tag and unceremoniously cram in
		//the contents
		$returnString = $this->Html->tag(
										'table',
										$tableContents,
										array(
											'class' => 'modelFields'
										)
									);
									
		//Return the table inside of a div so that it will display
		//nicely
		$returnString = $this->Html->tag(
										'div',
										$returnString,
										array(
											'class' => 'modelFieldsDiv'
										)
									);
		
		//Return the return string
		return $returnString;		
		
	}
	
}

?>