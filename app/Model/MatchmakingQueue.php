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
	
		
	//PUBLIC FUNCTION: checkQueue
	//We see if there's a someone to pair the user with already in the queue, if there
	//isn't then we place them in the queue
	public function checkQueue( $userUID, $teamUID ){
		
		//Look for available players to play against
		$availablePlayers = $this->find( 'first', array(
											'conditions' => array(
												'MatchmakingQueue.users_uid NOT' => $userUID
											),
                                            'fields' => array(
                                                'MatchmakingQueue.uid',
                                                'MatchmakingQueue.teams_uid',
                                                'MatchmakingQueue.users_uid'
                                            ),
											'order' => array(
												'MatchmakingQueue.created'
											)
										));
		
				
		//If we didn't find any available spots then we create a new record
		if( $availablePlayers == false ){
			return $this->placeInQueue( $userUID, $teamUID );
		}else{
			$this->delete( $availablePlayers['MatchmakingQueue']['uid'] );
			$defenderUserUID = $availablePlayers['MatchmakingQueue']['users_uid'];
			$defenderTeamUID = $availablePlayers['MatchmakingQueue']['teams_uid'];

            //Grab a game model instance and create them
            $gameModelInstance = ClassRegistry::init( 'Game' );
			return $gameModelInstance->newGame( $defenderUserUID, $defenderTeamUID, $userUID, $teamUID );
		}
				
	}
	
	//PUBLIC FUNCTION: getPendingGamesByUserUID
	//Return all of the pending games by their user UID
	public function getPendingGamesByUserUID( $userUID ){
		
		//Grab them all
		$pendingGames = $this->find( 'all', array(
							'conditions' => array(
								'MatchmakingQueue.users_uid' => $userUID
							)
						));
						
		//And returning them
		return $pendingGames;
		
	}
		
	//PUBLIC FUNCTION: placeInQueue
	//Create a record for the team in the queue
	public function placeInQueue( $userUID, $teamUID ){
		
		//Establish data to save
		$queueData = array( 
						'users_uid' => $userUID,
						'teams_uid' => $teamUID
						);
		
		//Create a record
		$this->create();
				
		//Return whether or not the save was successful
		return $this->save( $queueData );	
		
	}
	
}

