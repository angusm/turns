<?php
class Game extends AppModel {

	//Setup the associations for this model
	public $hasMany = array(
						'GameUnit' => array(
							'className' 	=> 'GameUnit',
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
		
	//PUBLIC FUNCTION: getActiveGames
	//Grab all of the active games that a user may be involved in
	public function getGamesForUser( $userUID ){
	
		//Run a find to get the user and all of their associated games
		$games = $this->find( 'all', array(
								'conditions' => array(
									'Game.active' => 1
								),
								'contain' => array(
									'GameUnit' => array(
										'conditions' => array(
											'GameUnit.turn' => 'Game.turn'
										),
										'TeamUnit' => array(
											'Team' => array(
												'User' => array(
												  	'conditions' => array(
														'User.uid' => $userUID
													)
												)
											)
										)
									)
								)
							));
							
		//Return the games we found	
		return $games;
		
		
	}
	
}

