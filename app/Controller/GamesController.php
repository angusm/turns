<?php
class GamesController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
    }
	
	//PUBLIC FUNCTION: clearGames
	//Clear out all of the data from active GamesController
	//FOR TESTING PURPOSES ONLY
	public function clearGames(){
		
		$gameModelInstance = ClassRegistry::init('Game');
		$games = $gameModelInstance->find( 'all' );
		foreach( $games as $gameToDelete ){
			
			$gameModelInstance->read( NULL, $gameToDelete['Game']['uid'] );
			$gameModelInstance->set( 'selected_unit_uid', NULL );
			$gameModelInstance->save();
						
		}
		
		$activeUserModel = ClassRegistry::init('ActiveUser');
		$activeUserModel->deleteAll('1=1');
		
		$userGameModel = ClassRegistry::init('UserGame');
		$userGameModel->deleteAll('1=1');
		
		$gameUnitModel = ClassRegistry::init('GameUnit');
		$gameUnitModel->deleteAll('1=1');
		
		$gameModelInstance->deleteAll('1=1');
		
		
	}
	
	//PUBLIC FUNCTION: getGameUpdate
	//Return JSON data to a client when they need to know about a 
	//possible new state of the game
	public function getGameUpdate(){
		
		//Grab the JSON Data
		$jsonData = $this->params['url'];
		
		//Grab the game uid
		$gameUID 			= $jsonData['gameUID'];
		
		//Get the last known turn for the given game
		$lastKnownTurn 		= $jsonData['lastKnownTurn'];
			
		//Get all that good game information
		$gameInformation 	= $this->Game->getUpdateInfo( $gameUID, $lastKnownTurn );
			
		//And everything else will be handled by the View and Javascript
		//Scary huh?
		$this->set( 'gameInformation', 	$gameInformation );
		$this->set( '_serialize', array(
						'gameInformation'
						)
					);
		
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
	
	//PUBLIC FUNCTION: processUnitMove
	//Process the given unit move
	public function processUnitMove(){
	
		//Grab the necessary data from the JSON
		$jsonData = $this->params['url'];
		
		//Grab the GameUnit UID, the targeted X and the targeted Y
		$gameUnitUID = $jsonData['gameUnitUID'];
		$targetedX	 = $jsonData['x'];
		$targetedY	 = $jsonData['y'];
		
		//Grab the user UID of the user making the request
		$userUID = $this->Auth->user( 'uid' );
					
		//Toss it over to the model, if the move was valid it will update the game 
		//and the game units and then return true, otherwise it will return false
		$validMove = $this->Game->validateMove(
											$gameUnitUID, 
											$targetedX, 
											$targetedY, 
											$userUID );
		
		$this->set( 'success', $validMove );
		$this->set(	'_serialize', array(
						'success'
					));
		
	}
	
}