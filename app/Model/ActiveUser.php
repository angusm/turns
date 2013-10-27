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
	
	//PUBLIC FUNCTION: setActiveUser
	//Set the active user for a given game on the given turn
	public function setActiveUser( $gameUID, $userGameUID, $turn ){
	
		$this->create();
		$this->set( 'games_uid', 		$gameUID );
		$this->set( 'user_games_uid',  $userGameUID );	
		$this->set(	'turn',				$turn );
		$this->save();
		
	}
	
}

