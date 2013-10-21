<?php
class TeamUnitPosition extends AppModel {
	
	//Setup the associations for UnitType
	public $belongsTo = array(
						'TeamUnit'	=> array(
							'className'		=> 'TeamUnit',
							'foreignKey'	=> 'team_units_uid'
						)						
					);

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 
			parent::__construct(); 
			
		//Setup the validation
		$this->validate = array(
			'x' => array(
				'default'	=> 	'-1',
				'message' 	=> 	parent::$numericMessage,
				'required' 	=>	true,
				'rule'		=> 	'numeric'
			),
			'y' => array(
				'default'	=> 	'-1',
				'message' 	=> 	parent::$numericMessage,
				'required' 	=>	true,
				'rule'		=> 	'numeric'
			)
		);

	}
	
	//PUBLIC FUNCTION: assignPosition
	//Assign a position to the given team units
	public function assignPosition( $teamUnitsUID, $x, $y ){
		
		//Make sure we don't already have a unit in that position
		//and that the unit is in the allowed range, we're going with
		//the starting positions of chess, so from 0,0 to 7,1
		if( $x > 7 or $y > 1 ){
			return false;
		}
		
		//Now we check that we don't already have a unit in that position
		//Since the unit in that position might not be of the same type
		//(and likely isn't if we've gotten enough unit variety in the game)
		//we need to grab all of the team unit uids that share the same
		//team as the given team unit UID
		
		//We'll need to grab the record for the given teamUnitsUID
		//To do this we'll need a team unit model 
		$teamUnitModelInstance = ClassRegistry::init( 'TeamUnit' );
		$passedTeamUnit = $teamUnitModelInstance->find( 'first', array(
													'conditions' => array(
														'TeamUnit.uid' => $teamUnitsUID
													)
												));
		
		//Next we'll find a list of valid UIDs
		$teamUID = $passedTeamUnit['TeamUnit']['teams_uid'];
		$validTeamUnitUIDs = $teamUnitModelInstance->find( 'list', array(
        								'fields' => array(
											'TeamUnit.uid', 
											'TeamUnit.uid'
										),
										'conditions' => array(
											'TeamUnit.teams_uid' => $teamUID
										)
									));
		
		//Check and see if we have a unit that's already in the position
		$inPlaceUnits = $this->find( 'all', array(
						'conditions' => array(
							'TeamUnitPosition.team_units_uid' 	=> $validTeamUnitUIDs,
							'TeamUnitPosition.x' 				=> $x,
							'TeamUnitPosition.y' 				=> $y
						)
					));

		//If there's already unit(s) in that position then remove them, both from
		//the position and from the team
		if( $inPlaceUnits != false ){
			
			foreach( $inPlaceUnits as $inPlaceUnit ){
			
				//First remove the unit from the position and then decrement the 
				//count of that team unit
				$this->read( NULL, $inPlaceUnit['TeamUnitPosition']['uid'] );
				$this->delete();
				
				//Now decrement the count of the unit
				$teamUnitModelinstance->decrementQuantityByUID( $inPlaceUnit['TeamUnitPosition']['team_units_uid'] );
				
			}
			
		}
		
		//Now we can assign the given team unit to the given position
		//We won't be incrementing the team count on this one as it should already
		//have been established.
		$this->create();
		$this->set( 'team_units_uid', 	$teamUnitsUID );
		$this->set( 'x',				$x );
		$this->set(	'y',				$y );
		$this->save();
		
		return true;
		
	}
	
	//PUBLIC FUNCTION: getUIDs
	//Return a list of all the UIDs
	public function getUIDs(){
	
		return $this->find( 'list', array(
				'fields' =>  'UnitType.uid'		
			));	
		
	}
	
	//PUBLIC FUNCTION: removeTeamUnit
	//Remove the given team unit
	public function removeTeamUnit( $teamUID, $unitTypeUID, $x, $y ){
	
		//Grab the team unit
		$teamUnitModelInstance = ClassRegistry::init( 'TeamUnit' );
		$teamUnit = $teamUnitModelInstance->find( 'first', array(
									'conditions' => array(
										'teams_uid'	 		=> $teamUID,
										'unit_types_uid'	=> $unitTypeUID
									)
								));
								
		//Grab the team unit position
		$teamUnitPosition = $this->find( 'first', array(
									'conditions' => array(
										'team_units_uid'	=> $teamUnit['TeamUnit']['uid'],
										'x'					=> $x,
										'y'					=> $y
									)
								));
								
		//Delete the record
		$this->read( null, $teamUnitPosition['TeamUnitPosition']['uid'] );
		$this->delete();
		
	}
	
}

