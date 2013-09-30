<?php

//This class will be used to make badass forms just the way I like them

App::uses('AppHelper', 'View/Helper');

class ModelUIHelper extends AppHelper {
	
	//We'll be using some of the HTML helper's functionality to do awesome stuff
  	var $helpers = array('Html');
	
	//PUBLIC FUNCTION: tableFromFind
	//Create an html table with the data retrieved laid out
	public function tableFromFind( 
									$findData,
									$fields ){
		
		//Grab the model name we're dealing with here
		$findDataKeys 	= array_keys( $findData[0] );
		$modelName		= $findDataKeys[0];
		
		//Setup an array to contain the table string
		$tableString = '';

		//Loop through the fields and spit out a table header row
		foreach( $fields as $displayValue => $fieldName ){
			$tableString .= $this->Html->tag(
										'th',
										$displayValue,
										array(
											'modelName' => $modelName,
											'fieldName'	=> $fieldName
										)
									);
		}
		
		//Add the headers to the string
		$tableString = $this->Html->tag(
									'tr',
									$tableString
									);
									
		//Loop through the various data and add the resulting rows
		//to the table.
		foreach( $findData as $modelData ){
			//Create a new string to store the resulting data row
			$rowString = '';
			
			//Loop through each field we need to display
			foreach( $fields as $displayValue => $fieldName ){
				
				$rowString .= $this->Html->tag(
												'td',
												$modelData[$modelName][$fieldName],
												array(
													'modelName' => $modelName,
													'fieldName' => $fieldName,
													'value'		=> $modelData[$modelName][$fieldName]
												)
											);
				
			}
		
			//Add the row we just setup into an actual row
			$rowString = $this->Html->tag(
											'tr',
											$rowString,
											array(
												'modelName' => $modelName,
												'uid'		=> $modelData[$modelName]['uid']
											)
										);
										
			//Slam the row string onto the end of the table string
			$tableString .= $rowString;
			
		}
												
		$tableString = $this->Html->tag(
									'table',
									$tableString
									);
		
		//Return the table string
		return $tableString;
		
	}
	
}

?>