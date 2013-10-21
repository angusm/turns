<?php
class Unit extends AppModel {

	//Setup the belongsTo
	public $belongsTo = array(
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
		
		//Add custom validation rules
		$this->validate = array();array_merge(
					array(
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
											'UnitArtSet' => array(
												'CardArtLayerSet' => array(
													'CardArtLayer'
												),
												'UnitArtSetIcon' => array(
													'Icon'
												)
											)
										)								
									)
								));
							
		//Go through each result and move the count to the model field	
		foreach( $unitList as $unitIndex => $unitData ){
			$unitList[$unitIndex]['Unit']['name']  = $unitList[$unitIndex]['UnitType']['name'];
			$unitList[$unitIndex]['Unit']['uid']  = $unitList[$unitIndex]['UnitType']['uid'];
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
		
		//Before we start knocking off tickets, we need to make sure that the
		//given Unit Type is valid, if it's not, we grab a new one recursively
		//until we get one that is. 
		//If this seems like it could cause some problems, well it sure as shit
		//could, that's why when we deploy we better make damn sure that we don't
		//deploy with broken ass units. Could probably even take this out for 
		//deployment.
		if( $unitTypeModelInstance->validateUnitType( $randomUnitTypeUID ) == false ){
			return $this->grantUserRandomUnit( $userUID );
		}
		
		//Decrement the Unit Type's ticket count
		$unitTypeModelInstance->decrementTicket( $randomUnitTypeUID );
	
		//Now we need to check if the user already has a Unit entry for this unit type.
		//If they do then we update the quantity, if they don't we create one
		$existingRecord = $this->find( 'first', array(
						'conditions' => array(
							'Unit.unit_types_uid' 	=> $randomUnitTypeUID,
							'Unit.users_uid' 		=> $userUID
						)
					));
					
		if( $existingRecord != false ){

			//Increment the quantity
			$nuQuantity = intval($existingRecord['Unit']['quantity']) + 1;
			
			$this->read( null, $existingRecord['Unit']['uid'] );
			$this->saveField( 'quantity', $nuQuantity );
						
		}else{
		
			//Now we need to make a new record and assign values to the new Unit
			$this->create();
			$this->set( 'unit_types_uid', 	$randomUnitTypeUID );
			$this->set( 'users_uid',		$userUID );
			$this->set( 'quantity',			1 );
			$this->save();
				
		}
		return true;
						
	}
	
}

