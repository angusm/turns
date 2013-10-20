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

		echo '<BR>Initiating User UID -> ' . $userUID;
		echo '<BR>Initiating Team UID -> ' . $teamUID;

		//Join the queue
		$success = $this->MatchmakingQueue->checkQueue( $userUID, $teamUID );
		
		$this->set( 'success',		$success );
		$this->set( '_serialize', 	array(
						'success'
					) );		
		
	}
	
}