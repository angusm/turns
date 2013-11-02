<?php
class GameUnit extends AppModel {

	//Setup the associations for this model
	public $belongsTo = array(
						'Game' => array(
							'className'		=> 'Game',
							'foreignKey'	=> 'games_uid'
						),
						'GameUnitStat' => array(
							'className'		=> 'GameUnitStat',
							'foreignKey'	=> 'game_unit_stats_uid'
						),
						'MovementSet' => array(
							'className'		=> 'MovementSet',
							'foreignKey'	=> 'movement_sets_uid'
						),
						'UnitArtSet' => array(
							'className'		=> 'UnitArtSet',
							'foreignKey'	=> 'unit_art_sets_uid'
						),
						'UnitType' => array(
							'className'		=> 'UnitType',
							'foreignKey'	=> 'unit_types_uid'
						),
						'User' => array(
							'className' 	=> 'User',
							'foreignKey'	=> 'users_uid'
						)
					);

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 

		//Call the parent constructor
		parent::__construct(); 
		
		$this->validate = array_merge(
					$this->validate
				);		

	}
	
	//PUBLIC FUNCTION: addToGameFromTeam
	//Take all the units in a given team and add them to a game
	public function addToGameFromTeam( $gameUID, $teamUID, $topPlayer=true ){
	
		//Setup whatever model instances we'll be needing
		$teamUnitModelInstance		= ClassRegistry::init( 'TeamUnit' );
		$gameUnitStatModelInstance 	= ClassRegistry::init( 'GameUnitStat' );
		
		//Grab the team's units
		$teamUnitTypes = $teamUnitModelInstance->getAllUnits( $teamUID );
		
		//Loop through all the unit types
		foreach( $teamUnitTypes as $teamUnitType ){
			//Loop through all of their positions
			foreach( $teamUnitType['TeamUnitPosition'] as $teamUnitPosition ){
			
				//If the player is the top player, then rotate their starting positions
				if( $topPlayer == true ){
					
					$teamUnitX = intval( $teamUnitPosition['x'] );
					$teamUnitY = 6 + intval( $teamUnitPosition['y'] );
					
				}else{
					
					$teamUnitX = 7 - intval( $teamUnitPosition['x'] );
					$teamUnitY = 1 - intval( $teamUnitPosition['y'] );
					
				}
			
				//Check if there's a game unit stat equivalent to the unit stat, if there is then we'll
				//use that for the new units, otherwise we'll create one in the rare event that this is
				//the first time this unit type with this unit stat has been used in a game.
				//May need to revisit this bullshit later when I'm older and wiser and less impatient and
				//have drunk fewer scotch ales
				
				$gameUnitStatUID = $gameUnitStatModelInstance->getUIDForUnitStat( $teamUnitType['UnitType']['UnitStat'] );
				
				//Setup the data
				$gameUnitData = array(
									'GameUnit' => array(
										'defense'					=> $teamUnitType['UnitType']['UnitStat']['defense'],
										'last_movement_angle'		=>  0,
										'last_movement_priority'	=>  0,
										'name'						=> $teamUnitType['UnitType']['name'],
										'turn'						=>  1,
										'x'							=> $teamUnitX,
										'y'							=> $teamUnitY,
										'games_uid' 				=> $gameUID,
										'game_unit_stats_uid'		=> $gameUnitStatUID,
										'unit_art_sets_uid'			=> $teamUnitType['UnitType']['unit_art_sets_uid'],
										'unit_types_uid'			=> $teamUnitType['UnitType']['uid'],
										'users_uid'					=> $teamUnitType['Team']['users_uid']
									)
								  );
									
				//Create a new record for the unit and save it
				$this->create();
				$this->save( $gameUnitData );
				
			}
											
		}
			
	}
	
	//PUBLIC FUNCTION: areAllLastMovementPrioritiesZero
	//Check and see if all the last movement priorities for the active turn
	//of the given game are zero
	public function areAllLastMovementPrioritiesZero( $gameUID, $turn ){
	
		//Grab any unit that doesn't have a unit move priority of 0 for the
		//given game on the given turn. If such a unit can be grabbed then
		//return false, otherwise return true
		$lastMovedUnit = $this->find( 'first', array(
									'conditions' => array(
										'GameUnit.games_uid' 					=> $gameUID,
										'GameUnit.turn'							=> $turn,
										'GameUnit.last_movement_priority NOT' 	=> 0
									)
								));	
								
		if( $lastMovedUnit == false ){
			return true;
		}else{
			return false;
		}
		
	}
	
	//PUBLIC FUNCTION: findAllMovementSets
	//Find all the movement sets associated with the GameUnit
	public function findAllMovementSets( $uid ){
	
		//Grab all the movement sets 
		$gameUnit = $this->find( 'first', array(
									'conditions' => array(
										'uid' => $uid
									),
									'contain' => array(
										'Unit' => array(
											'UnitType' => array(
												'UnitStat' => array(
													'UnitStatMovementSet' => array(
														'MovementSet'
													)
												)
											)
										)
									)
								));
									
		//Return the movement sets
		return $gameUnit['Unit']['UnitType']['UnitStat']['UnitStatMovementSet'];
		
	}
	
	//PUBLIC FUNCTION: findAllMovementSetsWithPriority
	//Find all the movement sets associated with the GameUnit that have
	//the given priority
	public function findAllMovementSetsWithPriority( $uid, $priority ){
	
		//Grab all the movement sets 
		$gameUnit = $this->find( 'first', array(
									'conditions' => array(
										'GameUnit.uid' => $uid
									),
									'contain' => array(
										'UnitType' => array(
											'UnitStat' => array(
												'UnitStatMovementSet' => array(
													'MovementSet' => array(
														'Movement' => array(
															'conditions' => array(
																'priority' => $priority
															),
															'MovementDirectionSet' => array(
																'DirectionSet' => array(
																	'DirectionSetDirection' => array(
																		'Direction'
																	)
																)
															)
														)
													)
												)
											)
										)
									)
								));
									
		//Return the movement sets
		return $gameUnit['UnitType']['UnitStat']['UnitStatMovementSet'];
		
	}
	
	//PUBLIC FUNCTION: findForMoveValidation
	//Find the GameUnit with the given UID
	public function findForMoveValidation( $uid ){
	
		//Return the GameUnit record
		$gameUnit =  $this->find( 'first', array(
								'conditions' => array(
									'GameUnit.uid' => $uid
								),
								'contain' => array(
									'Game' => array(
										'UserGame'
									)
								)
							));
	
		//Check to see if there's a unit in the same game
		//on the same turn that's already selected, if there is then
		//return false
		$otherUnit = $this->find( 'first', array(
									'conditions' => array(
										'GameUnit.turn' => $gameUnit['GameUnit']['turn'],
										'GameUnit.games_uid' => $gameUnit['GameUnit']['games_uid']
									)
								));
		if( isset( $otherUnit['GameUnit.uid'] ) and $otherUnit['GameUnit.uid'] != $uid ){
			return false;			
		}										
		
		return $gameUnit;
		
	}
	
	//PUBLIC FUNCTION: moveGameUnitsToNextTurn
	//Take all the units tied up in the current game and move them on to the next turn
	public function moveGameUnitsToNextTurn( 
										$gameUID, 
										$turn, 
										$gameUnitUID, 
										$targetX, 
										$targetY, 
										$angle, 
										$movePriority, 
										$movementSetUID ){
		
		//Setup the next turn
		$nuTurn 		= $turn + 1;
																	
		//Grab all of the relevant units on the current turn and then move 
		//them forward a turn, with the exception of the moved unit. 
		
		//Grab the relevant units
		$unitsToMove = $this->find( 'all', array(
							'conditions' => array(
								'GameUnit.games_uid'	=> $gameUID,
								'GameUnit.turn' 		=> $turn
							)
						));
						
		//Loop through the found units and bump them up as a new record
		foreach( $unitsToMove as $unitToMove ){
			
			//Store the original unit so we can record a record of it.
			$originalGameUnit = $unitToMove['GameUnit'];
			unset( $originalGameUnit['uid'] );
			$originalGameUnit = array(
									'GameUnit' => $originalGameUnit
								);
			$this->create();
			$originalGameUnit = $this->save( $originalGameUnit );
			
			//NOTE: MAKE NEW RECORD
			if( $unitToMove['GameUnit']['uid'] == $gameUnitUID ){
			
				//Update the values
				$unitToMove['GameUnit']['x'] 						= $targetX;
				$unitToMove['GameUnit']['y'] 						= $targetY;				
				$unitToMove['GameUnit']['last_movement_angle'] 		= $angle;
				$unitToMove['GameUnit']['last_movement_priority'] 	= $movePriority + 1;
				$unitToMove['GameUnit']['movement_sets_uid'] 		= $movementSetUID;
				
				//Now we need to check to make sure that the last active unit still
				//has moves left before it is finished moving. If it doesn't then we
				//set it's last_movement_priority back to 0.
				//To do this we need to find a movement tied to the given movement set
				//with the new priority
				$movementModelInstance = ClassRegistry::init( 'Movement' );
				$validNextMove = $movementModelInstance->find( 'first', array(
																	'conditions' => array(
																		'movement_sets_uid' => $movementSetUID,
																		'priority'			=> $unitToMove['GameUnit']['last_movement_priority']
																	)
																));
																
				if( $validNextMove == false ){
					$unitToMove['GameUnit']['last_movement_priority'] 	= 0;
					$unitToMove['GameUnit']['last_movement_angle'] 		= 0;
					$unitToMove['GameUnit']['movement_sets_uid'] 		= NULL;
				}					
			
			}
			
			//Update the turn
			$unitToMove['GameUnit']['turn'] = $nuTurn;
			
			//Move the unit up
			$unitToMove['GameUnit']['previous_game_unit_uid'] = $originalGameUnit['GameUnit']['uid'];
			$unitToMoveGameUnit = $unitToMove['GameUnit'];
			$unitToMove = array(
							'GameUnit' => $unitToMoveGameUnit
						);
			
			$this->read( NULL, $unitToMove['GameUnit']['uid'] );
			$this->save( $unitToMove );
						
		}			
		
	}
	
}

