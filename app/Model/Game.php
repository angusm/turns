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
	
	//PUBLIC FUNCTION: isMoveValid
	//Take in a gameUnit UID, the user who wants to make the move and 
	//a targeted X and Y. 
	//Then make sure that this move was possible. 
	//If it was we need to move the game up to the next turn.
	public function isMoveValid( $gameUnitUID, $targetX, $targetY, $userUID ){
		
		//Grab the gameUnit 
		$gameUnitModelInstance 	= ClassRegistry::init( 'GameUnit' );
		$gameUnit 				= $gameUnitModelInstance->findForMoveValidation( $gameUnitUID );
		$movePriority 			= $gameUnit['GameUnit']['last_movement_priority'] + 1;
		
		//Grab the staring positions and last move angle
		$startX 	= $gameUnit['GameUnit']['x'];
		$startY 	= $gameUnit['GameUnit']['y'];
		$lastAngle	= $gameUnit['GameUnit']['last_movement_angle'];
		$gameUID 	= $gameUnit['Game']['uid'];
		
		//Make sure the unit has a user game that belongs to the user and that
		//the game and the game unit are on the same turn
		if( $gameUnit['GameUnit']['users_uid'] != $userUID ){
			return false;
		}
		if( $gameUnit['Game']['turn'] != $gameUnit['GameUnit']['turn'] ){
			return false;	
		}
		
		//Grab an array of the possible movements
		if( $gameUnit['GameUnit']['movement_sets_uid'] == NULL ){
			
			$gameUnitMovementSets = $gameUnitModelInstance->findAllMovementSetsWithPriority( $gameUnitUID, $movePriority );
			
		}else{
			
			//Setup a MovementSet instance to grab this one set
			$movementSetModelInstance = ClassRegistry::init( 'MovementSet' );
			$gameUnitMovementSets = array(
								$movementSetModelInstance->findByUIDWithPriority( $gameUnit['GameUnit']['movement_sets_uid'], $movePriority )
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
		
		//Find the current game and move its turn up
		$game = $this->find( 'first', array(
								'conditions' => array(
									'Game.uid' => $gameUID
								)
							));
		$nuTurn = $game['Game']['turn'] + 1;
		$this->read( NULL, $gameUID );
		$this->set( 'turn', $nuTurn );
		$this->save();
		
	}
	
}

