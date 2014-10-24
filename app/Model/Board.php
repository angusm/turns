<?php

/**
 * Class Board
 */
class Board extends AppModel {

	//Setup the associations for this model
	public $belongsTo = [
						'Board' => [
							'className' 	=> 'Board',
							'foreignKey'	=> 'games_uid'
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
	
	//PUBLIC FUNCTION: newBoardForGame
	//Create a new board for the given game
	/**
	 * @return mixed
	 */
	public function newBoardForGame(){
		
		//Setup the default data for a new Game
		$newBoardData = [
							'height' 	=> 8,
							'width' 	=> 1		
						];
		
		//Create a new record
		$this->create();
		
		//Save the game
		return $this->save( $newBoardData );
		
	}
	
}

