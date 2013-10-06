<?php
class MatchmakingQueue extends AppModel {

	//Setup the associations for this model
	public $belongsTo = array(
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
	
		
	//PUBLIC FUNCTION: placeInQueue
	//We throw this user in the queue if we can't find anyone for them
	//to play against. This function will eventually have to be GREATLY
	//expanded. But until we get this game up and running we're going to
	//stick with the basics.
	public function placeInQueue( $userUID, $teamUID ){
		//
		
	
		//Look for available players to play against
		$availablePlayers = $this->find( 'all', array(
								'conditions' => array(
									'UserGame.users_uid' => $userUID
								)
							));		
							
		//Return the games we found	
		return $games;
		
		
	}
	
}

