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
										'UserGame' => array(
											'User',
											'GameUnit' => array(
												'conditions' => array(
													'GameUnit.turn' => $currentTurn
												),
												'Unit' => array(
													'UnitArtSet' => array(
														'UnitArtSetIcon' => array(
															'Icon' => array(
																'conditions' => array(
																	'icon_positions_uid' => 3
																)
															)
														)
													),
													'UnitType' => array(
														'UnitStat' => array(
															'UnitStatMovementSet' => array(
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
														)
													)
												)
											)
										),
										'Board'
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
	
		//Grab the game unit UID
		$gameUnitModelInstance = ClassRegistry::init( 'GameUnit' );
		
		
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
	
}

