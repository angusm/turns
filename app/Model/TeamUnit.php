<?php
class TeamUnit extends AppModel {

		public $belongsTo = array(
							'Unit' => array(
								'className' 	=> 'Unit',
								'foreignKey'	=> 'units_uid'
							),
							'Team'	=> array(
								'className'		=> 'Team',
								'foreignKey'	=> 'teams_uid'
							)
						);

		//Override the constructor so that we can set the variables our way
		//and not some punk ass way we don't much like.
		public function __construct() { 
	
			//Call the parent constructor
			parent::__construct(); 

		}
		
		//PUBLIC FUNCTION: addUnitToTeamByUnitTypeUID
		//Add a unit of the given type to the given team for the given user
		public function addUnitToTeamByUnitTypeUID( $unitTypeUID, $teamUID, $userUID ){
		
			//Create an instance of the Unit model
			$unitModelInstance = ClassRegistry::init( 'Unit' );
										
			//Grab all the units that are currently on the team
			$unitsAlreadyOnTeam = $this->find( 'list', array(
											'conditions' => array(
												'teams_uid' => $teamUID
											),
											'fields' => array(
												'units_uid'
											)
										));
			
			//Grab all the units that aren't already included
			$availableUnits 	= $unitModelInstance->find( 'all', array(
											'conditions' => array(
												'NOT'				=> array(
													'uid'			=> $unitsAlreadyOnTeam
												),
												'unit_types_uid'	=> $unitTypeUID,
												'users_uid'			=> $userUID
											)
										));
										
			//Check to make sure we have a unit available and if we do throw
			//it onto the pile
			if( $availableUnits != false ){
			
				return $this->addUnitToTeamByUnitUID( 
											$availableUnits[0]['Unit']['uid'], 
											$teamUID 
											);
				
			}else{
				
				return false;
			
			}
													
			
		}
		
		//PUBLIC FUNCTION: addUnitToTeamByUnitUID
		//Adds the given unit to the given team
		public function addUnitToTeamByUnitUID( $unitUID, $teamUID ){
		
			//Create a new record if one doesn't exist
			$exists = $this->find( 'first', array(
										'conditions' => array(
											'teams_uid' => $teamUID,
											'units_uid' => $unitUID
										)
									));
				
			//If there is no record create it
			if( $exists == false ){
			
				$this->create();
				$this->set( 'teams_uid', $teamUID );
				$this->set( 'units_uid', $unitUID );
				return $this->save();
			
			}else{
				
				return false;
				
			}
			
		}
		
		//PUBLIC FUNCTION: getTeamsForUnit
		//Grab all of the teams that a unit is on
		public function getTeamsForUnit( $unitUID ){
			
			//Do the find...
			$teamsForUnit = $this->find( 'all', array(
											'conditions' => array(
												'units_uid' => $unitsUID
											)
										));
										
			return $teamsForUnit;
		
		}
		
		//PUBLIC FUNCTION: getUnitsOnTeam
		//Grab all of the units that are posted on a given team
		public function getUnitsOnTeam( $teamUID ){
		
			//Do the find...
			$unitsOnTeam = $this->find( 'all', array(
										'conditions' => array(
											'teams_uid' => $teamUID
										),
										'contain' => array(
											'Unit' => array(
												'fields' => array(
													'unit_types_uid as uid',
													'name',
													'COUNT( * ) as count',
													'unit_types_uid'
												),
												'UnitType' => array(
													'fields' => array(
														'name'
													)
												)
											)
										),
										'group'			=> 'Unit.unit_types_uid'
									));
							
			//Go through each result and move the count to the model field	
			foreach( $unitsOnTeam as $unitIndex => $unitData ){
				$unitsOnTeam[$unitIndex]['Unit']['count'] = $unitsOnTeam[$unitIndex][0]['count'];
			}
									
			return $unitsOnTeam;
			
		}
		
		//PUBLIC FUNCTION: removeUnitFromTeamByUnitTypeUID
		//Remove a unit of the given type from the given team
		public function removeUnitFromTeamByUnitTypeUID( $unitTypeUID, $teamUID, $userUID ){
		
			//Create an instance of the Unit model
			$unitModelInstance = ClassRegistry::init( 'Unit' );
										
			//Grab all the units that are currently on the team
			$unitsAlreadyOnTeam = $this->find( 'list', array(
											'conditions' => array(
												'teams_uid' => $teamUID
											),
											'fields' => array(
												'units_uid'
											)
										));
			
			//Grab all the units that aren't already included
			$availableUnits 	= $unitModelInstance->find( 'first', array(
											'conditions' => array(
												'uid'				=> $unitsAlreadyOnTeam,
												'unit_types_uid'	=> $unitTypeUID,
												'users_uid'			=> $userUID
											)
										));
										
			//Check to make sure we have a unit available and if we do throw
			//it onto the pile
			if( $availableUnits != false ){
			
				return $this->removeUnitFromTeamByUnitUID( 
											$availableUnits['Unit']['uid'], 
											$teamUID 
											);
				
			}else{
				
				return false;
			
			}
			
			
		}
		
		//PUBLIC FUNCTION: removeUnitFromTeamByUnitUID
		//Remove the given unit from the given team
		public function removeUnitFromTeamByUnitUID( $unitUID, $teamUID ){
		
			//Grab the first record that matches the team and unit
			$exists = $this->find( 'first', array(
										'conditions' => array(
											'teams_uid' => $teamUID,
											'units_uid' => $unitUID
										)
									));
				
			//If a record exists delete it
			if( $exists != false ){
			
				$this->delete( $exists['TeamUnit']['uid'] );
			
			}else{
				
				return false;
				
			}			
			
		}
	
}

