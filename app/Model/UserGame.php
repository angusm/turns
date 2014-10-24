<?php

/**
 * Class UserGame
 */
class UserGame extends AppModel {

	//Setup the associations for this model
	public $hasMany = [
						'ActiveUser' => [
							'className' 	=> 'ActiveUser',
							'foreignKey'	=> 'user_games_uid'
						],
						'GameUnit' => [
							'className' 	=> 'GameUnit',
							'foreignKey'	=> 'user_games_uid'
						]
					];
	public $belongsTo = [
						'Game' => [
							'className' 	=> 'Game',
							'foreignKey'	=> 'games_uid'
						],
						'User' => [
							'className' 	=> 'User',
							'foreignKey'	=> 'users_uid'
						]
					];

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	/**
	 *
	 */
	public function __construct() {

		//Call the parent constructor
		parent::__construct(); 
		
		$this->validate = array_merge(
					$this->validate
				);		

	}
	
		
	//PUBLIC FUNCTION: getGamesByUserUID
	//Grab all of the active games that a user may be involved in
	/**
	 * @param $userUID
	 * @return array
	 */
	public function getGamesByUserUID( $userUID ){
	
		//Run a find to get the user and all of their associated games
		$games = $this->find( 'all', [
								'conditions' => [
									'users_uid' => $userUID
								]
							]);
							
		//Return the games we found	
		return $games;
	
	}
	
	//PUBLIC FUNCTION: newGame
	//Create a new record, so that, y'know, players can play
	/**
	 * @param $userUID
	 * @param $gameUID
	 * @param $priority
	 * @return mixed
	 */
	public function newGame( $userUID, $gameUID, $priority ){
	
		//Setup the model data for this new game
		$newGameData = [
						'users_uid'	=> $userUID,
						'games_uid' => $gameUID,
						'priority'	=> $priority
					];
					
		//Create a new record for the game
		$this->create();
		
		//Save all this luscious new data
		return $this->save( $newGameData );
		
	}
	
}

