<?php
class Unit extends AppModel {

	//Setup the belongsTo
	public $belongsTo = array(
							'UnitArtSet' => array(
								'className'		=> 'UnitArtSet',
								'foreignKey'	=> 'unit_art_sets_uid'
							),
							'UnitType' => array(
								'className'		=> 'UnitType',
								'foreignKey'	=> 'unit_types_uid'
							),
							'User' => array(
								'className'		=> 'User',
								'foreignKey'	=> 'users_uid'
							)
						);

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() {
		 
		 //Call the parent constructor
		parent::__construct(); 


		//Call the parent function to setup the key validation for the relation			
		parent::setupUIDRelation( array( 'User', 'UnitType', 'UnitArtSet' ) );
		
		//Add custom validation rules
		$this->validate = array_merge(
					array(
						'active' 		=> array(
							'default'	=> 	true,
							'message' 	=> 	parent::$booleanMessage,
							'rule'		=> 	'boolean'
						),
						'name' 			=> array(
							'default'	=> 	'Default',
							'message'	=>	parent::$alphaNumericMessage,
							'rule'		=>	parent::$alphaNumericWithSpacesValidationRule,
						),
						'unit_art_sets_uid'	=> array(
							'default'	=> 	'1',
							'message'	=>	'Must be valid Unit Art Set UID',
							'rule'		=>	'numeric'
						),
						'unit_types_uid'	=> array(
							'default'	=> 	'1',
							'message'	=>	'Must be valid Unit Type UID',
							'rule'		=>	'numeric'
						),
						'users_uid'	=> array(
							'default'	=> 	'1',
							'message'	=>	'Must be valid User UID',
							'rule'		=>	'numeric'
						)
					),
					$this->validate
				);

	}
	
	//PUBLIC FUNCTION: getUnitListForUserByUID
	//Grab a list of all the units (including quantities) belonging to a given
	//user
	public function getUnitListForUserByUID( $userUID ){
	
		//Do the find and return the results
		$unitList = $this->find( 'all', array(
									'conditions' => array(
										'users_uid' => $userUID
									),
									'contain' => array(
										'UnitType' => array(
											'fields' => array(
												'name'
											)
										)
									),											
									'fields' => array(
										'unit_types_uid as uid',
										'name',
										'COUNT( * ) as count',
										'unit_types_uid'
									),
									'group' => 'Unit.unit_types_uid'
								));
							
		//Go through each result and move the count to the model field	
		foreach( $unitList as $unitIndex => $unitData ){
			$unitList[$unitIndex]['Unit']['count'] = $unitList[$unitIndex][0]['count'];
			$unitList[$unitIndex]['Unit']['name']  = $unitList[$unitIndex]['UnitType']['name'];
		}
								
		return $unitList;	
		
	}
	
	//PUBLIC FUNCTION: getUnitsForUserByUID
	//Grab all of the units associated with the given user ID 
	public function getUnitsForUserByUID( $userUID ){
	
		//Do the find..
		$unitsForUser = $this->find( 'all', array(
							'conditions' => array(
								'users_uid' => $userUID
							)
						));

		//Return all of the units that a user has found
		return $unitsForUser;						
		
	}
	
	//PUBLIC FUNCTION: grantUserRandomUnit
	//Create a new unit to give to the user based off of a random Unit Type
	public function grantUserRandomUnit( $userUID ){
	
		//Grab an instance of the UnitType model so that we can grab a
		//random Unit Type to make this unit from
		$unitTypeModelInstance = ClassRegistry::init( 'UnitType' );
		$randomUnitTypeUID = $unitTypeModelInstance->getRandomUnitTypeByTicket();
		$unitTypeModelInstance->decrementTicket( $randomUnitTypeUID );
		
		
	}
	
}

