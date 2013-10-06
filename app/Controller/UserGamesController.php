<?php
class UserGamesController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
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
		
		print_r( $games );
		
		
	}
	
}