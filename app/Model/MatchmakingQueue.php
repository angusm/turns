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
			
			return $this->createGame( $defenderUserUID, $defenderTeamUID, $userUID, $teamUID );
		}
				
	}
	
	//PUBLIC FUNCTION: createGame
	//Create a new game and place both of these users and their teams in the
	//game
	public function createGame( 
						$defenderUserUID, 
						$defenderTeamUID, 
						$challengerUserUID, 
						$challengerTeamUID ){
		
		//Setup a new game
		$gameModelInstance = ClassRegistry::init( 'Game' );
		$createdGame = $gameModelInstance->newGame();
		
		//Tie the users to the new game
		$userGameModelInstance = ClassRegistry::init( 'UserGame' );
		$defenderNuGame 	= $userGameModelInstance->newGame( $defenderUserUID, 	$createdGame['Game']['uid'] );
		$challengerNuGame	= $userGameModelInstance->newGame( $challengerUserUID, 	$createdGame['Game']['uid'] );
		
		//Set the active player to the challenging player, as he's more likely to act first
		//having just joined the queue.
		//Yeah that sounds like a good justification for the arbitrariness of it all
		$activeUserModelInstance = ClassRegistry::init( 'ActiveUser' );
		$activeUserModelInstance->setActiveUser( $createdGame['Game']['uid'], $challengerNuGame['UserGame']['uid'], 1 );
		
		//Create game units for each game from their teams
		$gameUnitModelInstance = ClassRegistry::init( 'GameUnit' );
		$gameUnitModelInstance->addToGameFromTeam( $createdGame['Game']['uid'],  $defenderTeamUID, false );
		$gameUnitModelInstance->addToGameFromTeam( $createdGame['Game']['uid'],  $challengerTeamUID );
				
		//And just like that we've got a game. Boo yah.		
		return $createdGame;
		
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

