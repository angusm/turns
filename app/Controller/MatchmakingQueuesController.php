<?php
class MatchmakingQueuesController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
    }
	
	//PUBLIC FUNCTION: joinQueue
	//Add a user to the matchmaking queue, (or place them in a game right away
	//if we can)
	public function joinQueue(){
		
		//Grab the passed data
		$jsonData = $this->params['url'];
		
		//Grab the team uid from the JSON
		$teamUID = $jsonData['teamUID'];
		
		//Grab the user uid from the Auth component
		$userUID = $this->Auth->user('uid'); 	

		//Join the queue
		$this->MatchmakingQueue->checkQueue( $userUID, $teamUID );
		
		$this->set( '_serialize', array() );		
		
	}
	
}