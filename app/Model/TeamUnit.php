<?php

/**
 * Class TeamUnit
 */
class TeamUnit extends AppModel {

		public $belongsTo = [
							'UnitType' => [
								'className' 	=> 'UnitType',
								'foreignKey'	=> 'unit_types_uid'
							],
							'Team'	=> [
								'className'		=> 'Team',
								'foreignKey'	=> 'teams_uid'
							]
						];
						
		public $hasMany	= [
							'TeamUnitPosition' => [
								'className'		=> 'TeamUnitPosition',
								'foreignKey'	=> 'team_units_uid'
							]
						];

		//Override the constructor so that we can set the variables our way
		//and not some punk ass way we don't much like.
	/**
	 *
	 */
	public function __construct() {
	
			//Call the parent constructor
			parent::__construct(); 

		}
		
		//PUBLIC FUNCTION: addUnitToTeamByUnitTypeUID
		//Add a unit of the given type to the given team for the given user
	/**
	 * @param $unitTypeUID
	 * @param $teamUID
	 * @param $userUID
	 * @return bool|mixed
	 */
	public function addUnitToTeamByUnitTypeUID( $unitTypeUID, $teamUID, $userUID ){
		
			//Verify that the given user actually owns the given team
			//To do this we'll need a team model instance
			$teamModelInstance = ClassRegistry::init( 'Team' );
			//Run the find to check if the team with the given uid and user uid exists
			$userOwnsTeam = $teamModelInstance->find( 'first', [
														'conditions' => [
															'Team.uid' => $teamUID,
															'Team.users_uid' => $userUID
														]
													]);
													
			//Jump out if the user doesn't own the team
			if( $userOwnsTeam == false ){
				return false;
			}
			
			//Grab game constant info
			$gameConstantModelInstance = ClassRegistry::init( 'GameConstant' );
			$gameConstant	= $gameConstantModelInstance->find( 'first' );
			$maxTeamCost	= $gameConstant['GameConstant']['max_team_cost'];
			
			//Grab the current team cost
			$teamUnits = $this->find( 'all', [
											  'conditions' => [
												  'TeamUnit.teams_uid' => $teamUID
											  ],
											  'contain' => [
												  'UnitType' => [
													  'UnitStat'
												  ]
											  ]
										  ]);
			$teamCost = 0;
			foreach( $teamUnits as $teamUnit ){
				$teamCost += intval( $teamUnit['UnitType']['UnitStat']['teamcost'] ) * intval( $teamUnit['TeamUnit']['quantity'] );	
			}
			
			//Check if there's enough room in the team cost budget for the added unit
			$unitTypeModelInstance = ClassRegistry::init( 'UnitType' );
			$unitTypeRecord = $unitTypeModelInstance->find( 'first', [
																'conditions' => [
																	'UnitType.uid' => $unitTypeUID
																],
																'contain' => [
																	'UnitStat'
																]
															]);

			$newTeamCost = intval( $teamCost ) + intval( $unitTypeRecord['UnitStat']['teamcost'] );	
				
			if( $newTeamCost > $maxTeamCost ){
				return false;
			}
		
			//Check if there's a team unit record with the appropriate unit type and
			//team uid
			$appropriateRecord = $this->find( 'first', [
												'conditions' => [
													'TeamUnit.unit_types_uid'   => $unitTypeUID,
													'TeamUnit.teams_uid'		=> $teamUID
												]
											]);
		
			//If we don't have an appropriate record then create one and grab it
			if( $appropriateRecord == false ){
			
				//Create a new record
				$this->create();	
				$this->set( 'unit_types_uid', 	$unitTypeUID );
				$this->set( 'teams_uid',		$teamUID );
				$this->save();
				
				//Grab it back
				$appropriateRecord = $this->find( 'first', [
													'conditions' => [
														'TeamUnit.unit_types_uid'   => $unitTypeUID,
														'TeamUnit.teams_uid'		=> $teamUID
													]
												]);
												
			}
			
			//Now we need to make sure the user has enough of the given unit type to add 
			//another one to the given team.
		
			//Create an instance of the Unit model
			$unitModelInstance = ClassRegistry::init( 'Unit' );
					
			//Grab the number of units of the given type the user has
			$availableUnits 	= $unitModelInstance->find( 'first', [
											'conditions' => [
												'Unit.unit_types_uid'  	=> $unitTypeUID,
												'Unit.users_uid'		=> $userUID
											]
										]);

			//Now that we have the number of units that are already on the team and the number
			//that the player owns we can check if we still have one available to add
			if( $availableUnits['Unit']['quantity'] > $appropriateRecord['TeamUnit']['quantity'] ){

				//Setup the new quantity
				$nuQuantity = $appropriateRecord['TeamUnit']['quantity'] + 1;
				
				//Save the new quantity
				$this->read( NULL, $appropriateRecord['TeamUnit']['uid'] );
				$this->set( 'quantity', $nuQuantity );
				return $this->save();
							
			}else{
				
				//Return false if there aren't enough of the unit to add it
				return false;
				
			}													
			
		}
		
		//PUBLIC FUNCTION: decrementQuantityByUID
		//Remove one quantity of a Team Unit with the given UID
	/**
	 * @param $teamUnitUID
	 * @return bool
	 */
	public function decrementQuantityByUID( $teamUnitUID ){
		
			//Grab the given record
			$givenRecord = $this->find( 'first', [
											'conditions' => [
												'TeamUnit.uid'   => $teamUnitUID
											]
										]);
		
			//If we don't have an appropriate record then jump out and return false
			//After all we can't remove what doesn't exist
			if( $givenRecord == false ){
			
				return false;
			
			}

			//Setup the new quantity
			$nuQuantity = $givenRecord['TeamUnit']['quantity'] - 1;
			
			//If the new quantity is 0 or less than we just want to remove the attachment
			//of the given unit types to this team, otherwise we just assign the new 
			//quantity.
			if( $nuQuantity < 1 ){
				
				//Delete the record
				$this->read( NULL, $givenRecord['TeamUnit']['uid'] );
				$this->delete();
				
			}else{
				
				//Save the new quantity
				$this->read( NULL, $givenRecord['TeamUnit']['uid'] );
				$this->set( 'quantity', $nuQuantity );
				$this->save();
				
			}
			
			return true;					
			
		}
				
		//PUBLIC FUNCTION: getAllUnits
		//Grab all of the the units on a given team
		//Returning both the types of units on the team and the quantity
	/**
	 * @param $teamUID
	 * @return array
	 */
	public function getAllUnits( $teamUID ){
		
			//Do the find...
			$unitsOnTeam = $this->getUnitsOnTeam( $teamUID );
									
			return $unitsOnTeam;
			
		}
		
		//PUBLIC FUNCTION: getTeamsForUnitType
		//Grab all of the teams that a unit type is on
	/**
	 * @param $unitTypeUID
	 * @return array
	 */
	public function getTeamsForUnitType( $unitTypeUID ){
			
			//Do the find...
			$teamsForUnit = $this->find( 'all', [
											'conditions' => [
												'unit_types_uid' => $unitTypeUID
											]
										]);
										
			return $teamsForUnit;
		
		}
		
		//PUBLIC FUNCTION: getUnitsOnTeam
		//Grab all of the units that are posted on a given team
	/**
	 * @param $teamUIDs
	 * @return array
	 */
	public function getUnitsOnTeam( $teamUIDs ){
		
			//Do the find...
			$unitsOnTeam = $this->find( 'all', [
										'conditions' => [
                                            'OR' => [
											    'teams_uid' => $teamUIDs
                                            ]
										],
										'contain' => [
											'Team',
											'TeamUnitPosition',
											'UnitType' => [
												'UnitArtSet' => [
                                                    'UnitArtSetIcon' => [
                                                        'Icon'
                                                    ]
                                                ],
												'UnitStat' => [
													'UnitStatMovementSet'
												]
											]
										]
									]);
									
			return $unitsOnTeam;
			
		}
		
		//PUBLIC FUNCTION: removeUnitFromTeamByUnitTypeUID
		//Remove a unit of the given type from the given team
	/**
	 * @param $unitTypeUID
	 * @param $teamUID
	 * @param $userUID
	 * @return bool
	 */
	public function removeUnitFromTeamByUnitTypeUID( $unitTypeUID, $teamUID, $userUID ){
		
			//Verify that the given user actually owns the given team
			//To do this we'll need a team model instance
			$teamModelInstance = ClassRegistry::init( 'Team' );
			//Run the find to check if the team with the given uid and user uid exists
			$userOwnsTeam = $teamModelInstance->find( 'first', [
														'conditions' => [
															'Team.uid' => $teamUID,
															'Team.users_uid' => $userUID
														]
													]);
													
			//Jump out if the user doesn't own the team
			if( $userOwnsTeam == false ){
				return false;
			}
		
			//Check if there's a team unit record with the appropriate unit type and
			//team uid
			$appropriateRecord = $this->find( 'first', [
												'conditions' => [
													'TeamUnit.unit_types_uid'   => $unitTypeUID,
													'TeamUnit.teams_uid'		=> $teamUID
												]
											]);
		
			//If we don't have an appropriate record then jump out and return false
			//After all we can't remove what doesn't exist
			if( $appropriateRecord == false ){
			
				return false;
			
			}
			
			//Decrement the quantity, if the new quantity will be less than one then the 
			//record is deleted
			return $this->decrementQuantityByUID( $appropriateRecord['TeamUnit']['uid'] );		
			
		}
	
}

