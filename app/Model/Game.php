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
	
	//PUBLIC FUNCTION: newGame
	//Create a new game, y'know, so players can play.
	//And of course, so haters can hate. Cause haters gonna hate.
	public function newGame(){
		
		//Setup the default data for a new Game
		$newGameData = array(
							'turn' 		=> 1,
							'active' 	=> 1		
						);
		
		//Create a new record
		$this->create();
		
		//Save the game
		return $this->save( $newGameData );
		
	}
	
}

