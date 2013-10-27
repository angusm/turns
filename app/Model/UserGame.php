<?php
class UserGame extends AppModel {

	//Setup the associations for this model
	public $hasMany = array(
						'ActiveUser' => array(
							'className' 	=> 'ActiveUser',
							'foreignKey'	=> 'user_games_uid'
						),
						'GameUnit' => array(
							'className' 	=> 'GameUnit',
							'foreignKey'	=> 'user_games_uid'
						)
					);
	public $belongsTo = array(
						'Game' => array(
							'className' 	=> 'Game',
							'foreignKey'	=> 'games_uid'
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
	
		
	//PUBLIC FUNCTION: getGamesForUser
	//Grab all of the active games that a user may be involved in
	public function getGamesForUser( $userUID ){
	
		//Run a find to get the user and all of their associated games
		$games = $this->find( 'all', array(
								'conditions' => array(
									'UserGame.users_uid' => $userUID
								)
							));		
							
		//Return the games we found	
		return $games;
	
	}
	
	//PUBLIC FUNCTION: newGame
	//Create a new record, so that, y'know, players can play
	public function newGame( $userUID, $gameUID ){
	
		//Setup the model data for this new game
		$newGameData = array(
						'users_uid'	=> $userUID,
						'games_uid' => $gameUID
					);
					
		//Create a new record for the game
		$this->create();
		
		//Save all this luscious new data
		return $this->save( $newGameData );
		
	}
	
}

