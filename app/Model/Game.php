<?php
class Game extends AppModel {

	//Setup the associations for this model
	public $belongsTo 	= array(
							'Board' => array(
								'className'		=> 'Board',
								'foreignKey'	=> 'boards_uid'
							)
						);
						
	public $hasMany 	= array(
							'ActiveUser' => array(
								'className' 	=> 'ActiveUser',
								'foreignKey'	=> 'games_uid'
							),
							'GameUnit' => array(
								'className'		=> 'GameUnit',
								'foreignKey'	=> 'games_uid'
							),
							'UserGame' => array(
								'className' 	=> 'UserGame',
								'foreignKey'	=> 'games_uid'
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
	
	//PUBLIC FUNCTION: checkForGameOver
	//Check if the game still has living units on both sides, if it doesn't then shut it down
	public function checkForGameOver( $uid ){
	
		//Grab the game's information
		$game = $this->find( 'first', array(
										'conditions' => array(
											'Game.uid' => $uid
										)
									));
									
		//Grab the players for this game
		$userGameModelInstance = ClassRegistry::init( 'UserGame' );
		$userGames	= $userGameModelInstance->find('all', array(
														'conditions' => array(
															'UserGame.games_uid' => $uid
														)
													));
													
		//Loop through all of the user games and check if the users have at least one
		//unit still in the game left alive.
		//We setup a GameUnit model instance so we can check this and start with the assumption
		//that the game isn't over and then work to disprove it.
		$gameUnitModelInstance = ClassRegistry::init( 'GameUnit' );
		$gameStillActive = true;		
		foreach( $userGames as $userGame ){
			
			if( ! $gameUnitModelInstance->playerHasActiveUnit( $userGame['UserGame']['users_uid'], $uid, $game['Game']['turn'] ) ){
				$gameStillActive = false;
				break;
			}
			
		}
		
		return $gameStillActive;
		
	}
	
	//PUBLIC FUNCTION: getInfoForPlay
	//There's a bunch of information that's going to be necessary to 
	//actually play the game, so let's go ahead and get it all.
	//YEAH, TAKE IT!
	public function getInfoForPlay( $uid ){
		
		//Grab the game so that we can get the current turn
		$currentGame 		= $this->find( 'first', array(
										'conditions' => array(
											'Game.uid' => $uid
										)
									));
									
		//Grab the turn
		$currentTurn 		= $currentGame['Game']['turn'];
											
		//Time to run a fairly extensive find to grab all the information
		//about the game
		$gameInformation 	= $this->find( 'first', array(
									'conditions' => array(
										'Game.uid' => $uid
									),
									'contain' => array(
										'ActiveUser' => array(
											'conditions' => array(
												'ActiveUser.turn' => $currentTurn
											),
											'UserGame'
										),
										'Board' => array(
											'fields' => array(
												'height',
												'width'
											)
										),
										'GameUnit' => array(
											'conditions' => array(
												'GameUnit.turn' => $currentTurn
											),
											'GameUnitStat' => array(
												'fields' => array(
													'damage',
													'name'
												),
												'GameUnitStatMovementSet' => array(
													'MovementSet' => array(
														'Movement' => array(
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
											),
											'MovementSet',
											'UnitArtSet' => array(
												'UnitArtSetIcon' => array(
													'Icon' => array(
														'conditions' => array(
															'icon_positions_uid' => 3
														)
													)
												)
											)
										),
										'UserGame' => array(
											'User' => array(
												'fields' => 'username'
											)
										)
									)
								));

		//Alright now that we've pretty much downloaded the internet with
		//that fucking bloated find, let's return that mess so that we can
		//do more work with it somewhere the fuck else
		return $gameInformation;
		
	}
	
	//PUBLIC FUNCTION: getUpdateInfo
	//Much like getInfoForPlay but grabs as little information as possible
	public function getUpdateInfo( $uid, $lastKnownTurn ){
		
		//Grab the game so that we can get the current turn
		$currentGame 		= $this->find( 'first', array(
										'conditions' => array(
											'Game.uid' => $uid
										),
										'fields' => array(
											'turn'
										)
									));
									
		//Grab the turn
		$currentTurn 		= $currentGame['Game']['turn'];
									
		//If there hasn't been a change in the turn for the game then we
		//just return FALSE
		if( $lastKnownTurn == $currentTurn ){
			return FALSE;
		}
											
		//Grab the minimal information
		$gameInformation 	= $this->find( 'first', array(
									'conditions' => array(
										'Game.uid' => $uid
									),
									'contain' => array(
										'ActiveUser' => array(
											'conditions' => array(
												'ActiveUser.turn' => $currentTurn
											),
											'fields' => array(
												'ActiveUser.uid',
												'ActiveUser.games_uid',
												'ActiveUser.user_games_uid'
											),
											'UserGame' => array(
												'fields' => array(
													'UserGame.uid',
													'UserGame.users_uid'
												)
											)
										),
										'GameUnit' => array(
											'conditions' => array(
												'GameUnit.turn' => $currentTurn
											),
											'fields' => array(
												'GameUnit.uid',
												'GameUnit.damage',
												'GameUnit.defense',
												'GameUnit.last_movement_angle',
												'GameUnit.last_movement_priority',
												'GameUnit.movement_sets_uid',
												'GameUnit.previous_game_unit_uid',
												'GameUnit.game_unit_stats_uid',
												'GameUnit.users_uid',
												'GameUnit.x',
												'GameUnit.y'
											),
											'GameUnitStat' => array(
												'fields' => array(
													'GameUnitStat.uid',
													'GameUnitStat.damage',
													'GameUnitStat.defense',
													'GameUnitStat.name'
												),
												'GameUnitStatMovementSet' => array(
													'fields' => array(
														'GameUnitStatMovementSet.uid',
														'GameUnitStatMovementSet.movement_sets_uid',
														'GameUnitStatMovementSet.game_unit_stats_uid'
													),
													'MovementSet' => array(
														'fields' => array(
															'MovementSet.uid',
															'MovementSet.name'
														),
														'Movement' => array(
															'fields' => array(
																'Movement.uid',
																'Movement.movement_sets_uid',
																'Movement.must_move_all_the_way',
																'Movement.spaces'
															),
															'MovementDirectionSet' => array(
																'fields' => array(
																	'MovementDirectionSet.uid',
																	'MovementDirectionSet.movements_uid',
																	'MovementDirectionSet.direction_sets_uid'
																),
																'DirectionSet' => array(
																	'fields' => array(
																		'DirectionSet.uid'
																	),
																	'DirectionSetDirection' => array(
																		'fields' => array(
																			'DirectionSetDirection.uid',
																			'DirectionSetDirection.direction_sets_uid',
																			'DirectionSetDirection.directions_uid'
																		),
																		'Direction' => array(
																			'fields' => array(
																				'Direction.uid',
																				'Direction.angle'
																			)
																		)
																	)
																)
															)
														)
													)
												)
											),
											'MovementSet' => array(
												'fields' => array(
													'MovementSet.uid',
													'MovementSet.name'
												),
												'Movement' => array(
													'fields' => array(
														'Movement.uid',
														'Movement.movement_sets_uid',
														'Movement.must_move_all_the_way',
														'Movement.spaces'
													),
													'MovementDirectionSet' => array(
														'fields' => array(
															'MovementDirectionSet.uid',
															'MovementDirectionSet.movements_uid',
															'MovementDirectionSet.direction_sets_uid'
														),
														'DirectionSet' => array(
															'fields' => array(
																'DirectionSet.uid',
																'DirectionSet.name'
															),
															'DirectionSetDirection' => array(
																'fields' => array(
																	'DirectionSetDirection.uid',
																	'DirectionSetDirection.direction_sets_uid',
																	'DirectionSetDirection.directions_uid'
																),
																'Direction' => array(
																	'fields' => array(
																		'Direction.uid',
																		'Direction.angle'
																	)
																)
															)
														)
													)
												)
											)
										)
									),
									'fields' => array(
										'Game.turn',
										'Game.selected_unit_uid'
									)
								));

		//Loop through the game information and make sure there's at least
		//two players that still have units in the game
		$gameOver 			= true;
		$playerFound 		= NULL;
		$playersFoundCount 	= 0;
		foreach( $gameInformation['GameUnit'] as $gameUnit ){
			
			if( $gameUnit['users_uid'] != $playerFound and $gameUnit['defense'] > 0 ){
				if( $playersFoundCount == 0 ){
					$playersFoundCount += 1;
					$playerFound 		= $gameUnit['users_uid'];					
				}else{
					$gameOver = false;
					break;
				}				
			}
			
		}
		
		//Now that we know whether or not the game is over, we can store that
		//in the game information
		$gameInformation['game_over'] = $gameOver;

		//Alright now that we've pretty much downloaded the internet with
		//that fucking bloated find, let's return that mess so that we can
		//do more work with it somewhere the fuck else
		return $gameInformation;
		
	}
	
	//PUBLIC FUNCTION: isAUnitSelected
	//Return true or false depending on whether or not a unit is selected
	public function isAUnitSelected( $gameUID ){
		
		//Grab the relevant game
		$game = $this->find( 'first', array(
								'conditions' => array(
									'Game.uid'	=> $gameUID,
									'NOT'		=> array(
										'Game.selected_unit_uid' => null
									)
								)
							));
							
		//See if the unit is selected and return accordingly
		if( $game == false ){
			return false;
		}else{
			return TRUE;
		}
		
	}
	
	//PUBLIC FUNCTION: newGame
	//Create a new game, y'know, so players can play.
	//And of course, so haters can hate. Cause haters gonna hate.
	public function newGame(){
		
		//Setup the default data for a new Game
		$newGameData = array(
							'active' 		=> 1,
							'boards_uid'	=> 1,
							'turn' 			=> 1
						);
		
		//Create a new record
		$this->create();
		
		//Save the game
		return $this->save( $newGameData );
		
	}
	
	//PUBLIC FUNCTION: updateGame
	//Update the game with the given move
	public function updateGame( 
							$gameUID, 
							$turn, 
							$gameUnitUID, 
							$targetX, 
							$targetY, 
							$angle, 
							$movePriority, 
							$movementSetUID ){
		
		//Update the moved unit and then update all of the others
		$gameUnitModelInstance = ClassRegistry::init( 'GameUnit' );
		$gameUnitModelInstance->moveGameUnitsToNextTurn( 
							$gameUID, 
							$turn, 
							$gameUnitUID, 
							$targetX, 
							$targetY, 
							$angle, 
							$movePriority, 
							$movementSetUID );
							
		//Update the active user model to record who was the active user
		//on the new turn
		$activeUserModelInstance = ClassRegistry::init( 'ActiveUser' );
		$activeUserModelInstance->moveToNextTurn( $gameUID );
		
		//Find the current game and move its turn up
		$game = $this->find( 'first', array(
								'conditions' => array(
									'Game.uid' => $gameUID
								)
							));
		$nuTurn = $game['Game']['turn'] + 1;
		
		//One last step before we update the game is to see if there are still units
		//alive on both sides.
		$gameOver = $this->checkForGameOver( $gameUID );
		
		//Update the game
		$this->read( NULL, 		$gameUID );
		$this->set( 'turn', 	$nuTurn );
		$this->set( 'active', 	$gameOver );
		$this->save();
		
	}
	
	//PUBLIC FUNCTION: validateMove
	//Take in a gameUnit UID, the user who wants to make the move and 
	//a targeted X and Y. 
	//Then make sure that this move was possible. 
	//If it was we need to move the game up to the next turn.
	public function validateMove( $gameUnitUID, $targetX, $targetY, $userUID ){
				
		//Grab the gameUnit 
		$gameUnitModelInstance 	= ClassRegistry::init( 'GameUnit' );
		$gameUnit 				= $gameUnitModelInstance->findForMoveValidation( $gameUnitUID );
		
		//The gameUnit find will return false if there's another unit in this game that
		//is currently selected.
		if( $gameUnit == false ){
			return false;
		}
		
		//Make sure the unit has a user game that belongs to the user and that the game 
		//and the game unit are on the same turn
		if( $gameUnit['GameUnit']['users_uid'] != $userUID ){
			return false;
		}
		if( $gameUnit['Game']['turn'] != $gameUnit['GameUnit']['turn'] ){
			return false;	
		}
				
		//Make sure the given user is the active user for the current game
		$activeUserModelInstance = ClassRegistry::init( 'ActiveUser' );
		$activePlayer = $activeUserModelInstance->findActiveUser( $gameUnit['Game']['uid'], $gameUnit['Game']['turn'] );
		if( ! isset( $activePlayer ) or $activePlayer['UserGame']['users_uid'] != $userUID ){
			return false;
		}
				
		//Get the new priority for this unit
		$movePriority 			= $gameUnit['GameUnit']['last_movement_priority'];
		
		//Grab the staring positions and last move angle
		$startX 	= $gameUnit['GameUnit']['x'];
		$startY 	= $gameUnit['GameUnit']['y'];
		$lastAngle	= $gameUnit['GameUnit']['last_movement_angle'];
		$gameUID 	= $gameUnit['Game']['uid'];
		
		
		//Grab an array of the possible movements
		if( $movePriority == 0 ){
			
			$gameUnitMovementSets = $gameUnitModelInstance->findAllMovementSetsWithPriority( $gameUnitUID, $movePriority );
			
		}else{

			//Setup a MovementSet instance to grab this one set
			$movementSetModelInstance = ClassRegistry::init( 'MovementSet' );
			$gameUnitMovementSets = array( 
										array(
											'MovementSet' => array_merge(
												array( 'uid' => $gameUnit['GameUnit']['movement_sets_uid'] ),
												$movementSetModelInstance->findByUIDWithPriority( $gameUnit['GameUnit']['movement_sets_uid'], $movePriority )
											)
										)
								);
														
		}
				
		//See if any of the possible movements can get the unit to the target
		foreach( $gameUnitMovementSets as $movementSets ){
			foreach( $movementSets['MovementSet']['Movement'] as $movement ){
		
				//Grab the spaces this move can travel along with whether it
				//must travel them all or only some
				$spaces				= intval( $movement['spaces'] );
				$mustMoveAllTheWay	= intval( $movement['must_move_all_the_way'] );
								
				//Loop through all the movement direction sets and direction sets
				foreach( $movement['MovementDirectionSet'] as $movementDirectionSet ){
					foreach( $movementDirectionSet['DirectionSet']['DirectionSetDirection'] as $directionSetDirection ){
					
						//Grab the direction
						$direction = $directionSetDirection['Direction'];
						
						//Get the angle 
						$angleToCheck = $direction['angle'] + $lastAngle;
						
						//Grab the x and y direction we'll be checking
						$xDirection =   intval( round( sin( $angleToCheck * ( pi() / 180 ) ) ) );
						$yDirection = - intval( round( cos( $angleToCheck * ( pi() / 180 ) ) ) );
						
						//Check if the unit has to move all the way						
						if( $mustMoveAllTheWay ){
							
							//Find the x and y to check and if they're successful then we can return true
							$xToCheck = $startX + ( $spaces * $xDirection );
							$yToCheck = $startY + ( $spaces * $yDirection );
								
							//If we have a match, update the game and then return true
							if( $xToCheck == $targetX and $yToCheck == $targetY ){
							
								$this->updateGame( 
										$gameUID, 
										$gameUnit['GameUnit']['turn'], 
										$gameUnitUID, 
										$targetX, 
										$targetY, 
										$angleToCheck,
										$movePriority,
										$movementSets['MovementSet']['uid'] );
								return true;
								
							}
								
						}else{	
						
							//Check the x and y of each space we move along
							for( $spaceCounter = 1; $spaceCounter <= $spaces; $spaceCounter++ ){
								
								$xToCheck = $startX + ( $spaceCounter * $xDirection );
								$yToCheck = $startY + ( $spaceCounter * $yDirection );
								
								//If we have a match, update the game and then return true
								if( $xToCheck == $targetX and $yToCheck == $targetY ){
								
									$this->updateGame( 
										$gameUID, 
										$gameUnit['GameUnit']['turn'], 
										$gameUnitUID, 
										$targetX, 
										$targetY, 
										$angle,
										$movePriority,
										$movementSets['MovementSet']['uid'] );
									return true;
									
								}
								
							}
							
						}	
					
					}
							
				}					
			}
		}
		
	}
	
}

