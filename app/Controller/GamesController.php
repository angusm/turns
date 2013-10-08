<?php
class GamesController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
    }
	
	//PUBLIC FUNCTION: playGame
	//Create a nice little view with a playable game in it
	public function playGame(){
		
		//Grab the JSON Data
		$jsonData = $this->params['url'];
		
		//Grab the game uid
		$gameUID = $jsonData['gameUID'];
		$userUID = $this->Auth->user('uid');
		
		//Get all that good game information
		$gameInformation = $this->Game->getInfoForPlay( $gameUID );
			
		//And everything else will be handled by the View and Javascript
		//Scary huh?
		$this->set( 'gameInformation', 	$gameInformation );
		$this->set( 'userUID',			$userUID );
	}
	
}