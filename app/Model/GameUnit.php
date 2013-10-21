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
	public function addToGameFromTeam( $gameUID, $teamUID ){
	
		//Setup whatever model instances we'll be needing
		$teamUnitModelInstance		= ClassRegistry::init( 'TeamUnit' );
		$gameUnitStatModelInstance 	= ClassRegistry::init( 'GameUnitStat' );
		
		//Grab the team's units
		$teamUnitTypes = $teamUnitModelInstance->getAllUnits( $teamUID );
		
		//Loop through all the unit types
		foreach( $teamUnitTypes as $teamUnitType ){
		
			//Check if there's a game unit stat equivalent to the unit stat, if there is then we'll
			//use that for the new units, otherwise we'll create one in the rare event that this is
			//the first time this unit type with this unit stat has been used in a game.
			//May need to revisit this bullshit later when I'm older and wiser and less impatient and
			//have drunk fewer scotch ales
			
			$gameUnitStatUID = $gameUnitStatModelInstance->getUIDForUnitStat( $teamUnitType['UnitType']['UnitStat'] );
					
			//For each quantity of unit type we need to make a game unit
			for( $newUnitCounter = 0; $newUnitCounter < $teamUnitType['TeamUnit']['quantity']; $newUnitCounter++ ){
			
				//Setup the data
				$gameUnitData = array(
									'GameUnit' => array(
										'defense'					=> $teamUnitType['UnitType']['UnitStat']['defense'],
										'last_movement_angle'		=>  0,
										'last_movement_priority'	=>  0,
										'name'						=> $teamUnitType['UnitType']['name'],
										'turn'						=>  1,
										'x'							=> -1,
										'y'							=> -1,
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
										'uid' => $uid
									),
									'contain' => array(
										'Unit' => array(
											'UnitType' => array(
												'UnitStat' => array(
													'UnitStatMovementSet' => array(
														'MovementSet' => array(
															'conditions' => array(
																'priority' => $priority
															)
														)
													)
												)
											)
										)
									)
								));
									
		//Return the movement sets
		return $gameUnit['Unit']['UnitType']['UnitStat']['UnitStatMovementSet'];
		
	}
	
	//PUBLIC FUNCTION: findForMoveValidation
	//Find the GameUnit with the given UID
	public function findForMoveValidation( $uid ){
	
		//Return the GameUnit record
		return $this->find( 'first', array(
								'conditions' => array(
									'uid' => $uid
								),
								'contain' => array(
									'UserGame' => array(
										'Game'
									)
								)
							));	
		
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
		$nuTurn = $turn + 1;
		$nuMovePriority = $movePriority + 1;
		
		//Grab all of the user games tied to the given game UID
		$userGameModelInstance = ClassRegistry::init( 'UserGame' );
		$relevantUserGameUIDs = $userGameModelInstance->find( 'list', array(
																'conditions' => array(
																	'UserGame.games_uid' => $gameUID
																),
																'fields' => array(
																	'UserGame.uid'
																)
															));
															
		//Loop through the user game UIDs and grab all of the relevant units on the 
		//current turn and then move them forward a turn, with the exception of the 
		//moved unit. 
		foreach( $relevantUserGameUIDs as $relevantUID ){
		
			//Grab the relevant units
			$unitsToMove = $this->find( 'all', array(
								'conditions' => array(
									'GameUnit.user_games_uid'	=> $relevantUID,
									'GameUnit.turn' 			=> $turn,
									'GameUnit.uid NOT'			=> $gameUnitUID
								)
							));
							
			//Loop through the found units and bump them up as a new record
			foreach( $unitsToMove as $unitToMove ){
				
				//NOTE: MAKE NEW RECORD
				
				//Move the unit up
				$this->read( NULL, $unitToMove['GameUnit']['uid'] );
				$this->set( 'turn', $nuTurn );
				$this->save();
				
			}			
			
		}
		
		//NOTE: MAKE NEW RECORD
		
		//Now we finally get to update the unit to move
		$this->read( NULL, $gameUID );
		$this->set( 'turn', $nuTurn );
		$this->set( 'x', $targetX );
		$this->set( 'y', $targetY );
		$this->set( 'last_movement_angle', $angle );
		$this->set( 'last_movement_priotiy', $nuMovePriority );
		$this->save();
		
	}
	
}

