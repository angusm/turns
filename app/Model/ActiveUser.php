<?php
class ActiveUser extends AppModel {

	public $primaryKey = 'uid';
	
	public $belongsTo 	= array(
							'Game' => array(
								'className'		=> 'Game',
								'foreignKey'	=> 'games_uid'
							),
							'UserGame' => array(
								'className'		=> 'UserGame',
								'foreignKey'	=> 'user_games_uid'
							)
						);
	
	//PUBLIC FUNCTION: findActiveUser
	//Find the active user for the given game on the given turn
	public function findActiveUser( $gameUID, $turn ){
		
		return $this->find( 'first', array(
								'conditions' => array(
									'ActiveUser.games_uid' 		=> $gameUID,
									'ActiveUser.turn'			=> $turn
								),
								'contain' => array(
									'UserGame'
								)
							));
		
	}	
	
	//PUBLIC FUNCTION: moveToNextTurn
	//Record the active user for the next turn of the given game
	public function moveToNextTurn( $gameUID ){
	
		//Find the data for the latest turn
		$latestTurn = $this->find( 'first', array(
										'conditions' => array(
											'ActiveUser.games_uid' => $gameUID
										),
										'order' => array(
											'ActiveUser.turn DESC'
										)										
									));
	
		//Grab the data and setup the next turn and then check and see if we
		//need to move to the next active user
		$nextTurn 			= $latestTurn['ActiveUser']['turn'] + 1;
		$nextUserGameUID 	= $latestTurn['ActiveUser']['user_games_uid'];
		
		//In order to see if the active user is changing this turn we need
		//to check with the game units for this game, if they all have a 
		//last move priority of 0 then it's time to move to the next turn
		$gameUnitModelInstance = ClassRegistry::init( 'GameUnit' );
		if( $gameUnitModelInstance->areAllLastMovementPrioritiesZero( $gameUID, $nextTurn ) ){
			
			//If we've got to move to the next user then we have a bit more
			//work cut out for us, we need to grab the user game for the current
			//active user, get it's priority and then move to the next user
			//game with a higher priority.
			//If no user game with a higher priority can be found then we need to
			//move to the user game with 1 priority.
			$userGameModelInstance = ClassRegistry::init( 'UserGame' );
			$currentActiveUserGame = $userGameModelInstance->find( 'first', array(
																		'conditions' => array(
																			'UserGame.uid' => $latestTurn['ActiveUser']['user_games_uid']
																		)
																	));
										
			//Find the next highest priority
			$nextActiveUserGame = $userGameModelInstance->find( 'first', array(
																	'conditions' => array(
																		'UserGame.games_uid' 	=> $gameUID,
																		'UserGame.priority >' 	=> $currentActiveUserGame['UserGame']['priority']
																	),
																	'order' => array(
																		'UserGame.priority'
																	)
																));
																
			//If no next highest priority can be found then grab the one with 1 priority
			if( $nextActiveUserGame == false ){
			
				$nextActiveUserGame = $userGameModelInstance->	find( 'first', array(
																	'conditions' => array(
																		'UserGame.games_uid' 	=> $gameUID,
																		'UserGame.priority' 	=> 1
																	)
																));
			
			}
			
			//Set the next user game UID
			$nextUserGameUID = $nextActiveUserGame['UserGame']['uid'];
																	
			
		}
	
		$this->setActiveUser( $gameUID, $nextUserGameUID, $nextTurn );
	
	}
	
	//PUBLIC FUNCTION: setActiveUser
	//Set the active user for a given game on the given turn
	public function setActiveUser( $gameUID, $userGameUID, $turn ){
	
		$this->create();
		$this->set( 'games_uid', 		$gameUID );
		$this->set( 'user_games_uid',  	$userGameUID );	
		$this->set(	'turn',				$turn );
		$this->save();
		
	}
	
}

