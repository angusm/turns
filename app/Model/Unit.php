<?php
class Unit extends AppModel {

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() {
		 
		 //Call the parent constructor
		parent::__construct(); 

		//Call the parent function to setup the key validation for the relation			
		parent::setupUIDRelation( array( 'User', 'UnitType', 'UnitArtSet' ) );
		
		$this->validate = array_merge( 
					array(
						'image' => array(
							'required' 	=>	true
						 )
					),
					$this->validate
				);

		//Add custom validation rules
		$this->validate = array_merge(
					array(
						'active' 	=> array(
							'rule'		=> 	'boolean',
							'message' 	=> 	parent::$booleanMessage
						),
						'name' 		=> array(
							'rule'		=>	'alphaNumeric',
							'message'	=>	parent::$alphaNumericMessage
						)
					),
					$this->validate
				);

	}
	
}

