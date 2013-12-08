<?php
class UserGamesController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
    }
	
	//PUBLIC FUNCTION: getAvailableGamesList
	//Return a list of all the games the user is currently involved in
	//along with a list of all of the entries the user has in the 
	//matchmaking queue, i.e. their pending matches.
	public function getAvailableGamesList(){
		
		//Grab the passed data 
		$jsonData = $this->params['url'];
		
		//Grab the UID for the currently logged in user if one wasn't specified
		if( isset( $jsonData['userUID'] ) ){
			$userUID = $jsonData['userUID'];	
		}else{
			$userUID = $this->Auth->user('uid'); 	
		}
		
		//Grab the games
		$games = $this->UserGame->getGamesForUser( $userUID );
		
		//Grab all of the pending matches
		$matchmakingQueueModelInstance = ClassRegistry::init( 'MatchmakingQueue' );
		$pendingMatches = $matchmakingQueueModelInstance->getPendingGamesByUserUID( $userUID );
		
		//And toss it all back
		$this->set( 'games', $games );
		$this->set( 'pendingMatches', $pendingMatches );
		$this->set( '_serialize', array(
						'games',
						'pendingMatches'
					));
		
	}
	
	//PUBLIC FUNCTION: getGamesForUser
	//Grab all the games for the given user, or if no user is
	//given then grab all the games for the currently logged in user
	public function getGamesForUser(){
		
		//Grab the passed data
		$jsonData = $this->params['url'];
		
		//Check to see if we were given a user or if we need to grab
		//the user from the authentication component
		if( isset( $jsonData['userUID'] ) ){
			$userUID = $jsonData['userUID'];	
		}else{
			$userUID = $this->Auth->user('uid'); 	
		}
		
		//Grab the games
		$games = $this->UserGame->getGamesForUser( $userUID );
		
		//Set the games to the view
		$this->set( 'games', $games );
		$this->set( '_serialize', array(
						'games'
					));
		
	}
	
}