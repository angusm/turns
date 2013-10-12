<?php
class GameUnit extends AppModel {

	//Setup the associations for this model
	public $belongsTo = array(
						'UserGame' => array(
							'className' 	=> 'UserGame',
							'foreignKey'	=> 'user_games_uid'
						),
						'Unit' => array(
							'className' 	=> 'Unit',
							'foreignKey'	=> 'units_uid'
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
	
		//Grab the team's units
		$teamUnitModelInstance = ClassRegistry::init( 'TeamUnit' );
		$teamUnits = $teamUnitModelInstance->getAllUnits( $teamUID );
		
		//Loop through all the units
		foreach( $teamUnits as $teamUnit ){
		
			//Setup the data
			$gameUnitData = array(
									'user_games_uid' 	=> $gameUID,
									'units_uid'			=> $teamUnit['TeamUnit']['units_uid'],
									'turn'				=> 1,
									'x'					=> -1,
									'y'					=> -1,
									'defense'			=> $teamUnit['Unit']['UnitType']['UnitStat']['defense']
								);
								
			//Create a new record for the unit and save it
			$this->create();
			$this->save( $gameUnitData );
											
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

