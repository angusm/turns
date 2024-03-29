<?php

/**
 * Class ActiveUser
 */
class ActiveUser extends AppModel {

	public $primaryKey = 'uid';
	
	public $belongsTo 	= [
							'Game' => [
								'className'		=> 'Game',
								'foreignKey'	=> 'games_uid'
							],
							'UserGame' => [
								'className'		=> 'UserGame',
								'foreignKey'	=> 'user_games_uid'
							]
						];
	
	//PUBLIC FUNCTION: findActiveUser
	//Find the active user for the given game on the given turn
	/**
	 * @param $gameUID
	 * @param $turn
	 * @return array
	 */
	public function findActiveUser( $gameUID, $turn ){
		
		return $this->find( 'first', [
								'conditions' => [
									'ActiveUser.games_uid' 		=> $gameUID,
									'ActiveUser.turn'			=> $turn
								],
								'contain' => [
									'UserGame'
								]
							]);
		
	}	
	
	//PUBLIC FUNCTION: moveToNextTurn
	//Record the active user for the next turn of the given game
	/**
	 * @param $gameUID
	 */
	public function moveToNextTurn( $gameUID ){
	
		//Find the data for the latest turn
		$latestTurn = $this->find( 'first', [
										'conditions' => [
											'ActiveUser.games_uid' => $gameUID
										],
										'order' => [
											'ActiveUser.turn DESC'
										]
									]);
	
		//Grab the data and setup the next turn and then check and see if we
		//need to move to the next active user
		$nextTurn 			= $latestTurn['ActiveUser']['turn'] + 1;
		$nextUserGameUID 	= $latestTurn['ActiveUser']['user_games_uid'];
		
		//In order to see if the active user is changing this turn we need
		//to check with the game to see if there's a selected unit. If one 
		//isn't selected we move to the next user
		$gameModelInstance = ClassRegistry::init( 'Game' );
		if( ! $gameModelInstance->isAUnitSelected( $gameUID ) ){
			
			//If we've got to move to the next user then we have a bit more
			//work cut out for us, we need to grab the user game for the current
			//active user, get it's priority and then move to the next user
			//game with a higher priority.
			//If no user game with a higher priority can be found then we need to
			//move to the user game with 1 priority.
			$userGameModelInstance = ClassRegistry::init( 'UserGame' );
			$currentActiveUserGame = $userGameModelInstance->find( 'first', [
																		'conditions' => [
																			'UserGame.uid' => $latestTurn['ActiveUser']['user_games_uid']
																		]
																	]);
										
			//Find the next highest priority
			$nextActiveUserGame = $userGameModelInstance->find( 'first', [
																	'conditions' => [
																		'UserGame.games_uid' 	=> $gameUID,
																		'UserGame.priority >' 	=> $currentActiveUserGame['UserGame']['priority']
																	],
																	'order' => [
																		'UserGame.priority'
																	]
																]);
																
			//If no next highest priority can be found then grab the one with 1 priority
			if( $nextActiveUserGame == false ){
			
				$nextActiveUserGame = $userGameModelInstance->	find( 'first', [
																	'conditions' => [
																		'UserGame.games_uid' 	=> $gameUID,
																		'UserGame.priority' 	=> 1
																	]
																]);
			
			}
			
			//Set the next user game UID
			$nextUserGameUID = $nextActiveUserGame['UserGame']['uid'];
																	
			
		}
	
		$this->setActiveUser( $gameUID, $nextUserGameUID, $nextTurn );
	
	}
	
	//PUBLIC FUNCTION: setActiveUser
	//Set the active user for a given game on the given turn
	/**
	 * @param $gameUID
	 * @param $userGameUID
	 * @param $turn
	 */
	public function setActiveUser( $gameUID, $userGameUID, $turn ){
	
		$this->create();
		$this->set( 'games_uid', 		$gameUID );
		$this->set( 'user_games_uid',  	$userGameUID );	
		$this->set(	'turn',				$turn );
		$this->save();
		
	}
	
}

