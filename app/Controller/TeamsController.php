<?php

class TeamsController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('');
    }

	//PUBLIC FUNCTION: index
	//Essentially a homepage for the users where they can
	//view all their lovely stuff.
    public function index() {
		//Empty... for now!		
    }
	
	//PUBLIC FUNCTION: add
	//Sets up the page where a user can register 
    public function add() {
		
		//If we're dealing with a posted message
        if ($this->request->is('post')) {
			
			//Create the record
            $this->Team->create();
			
			//See if we can save the user using the given data...
			if ($this->Team->save($this->request->data)) {
				//If the user has been saved, indicate as much and do a
				//redirect.
				$this->Session->setFlash(__('Team Saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				//If the user couldn't be saved then indicate as much.
				$this->Session->setFlash(__('Couldn\'t save that team, they\'re all dead now and it\'s all your fault.'));
			}
        }
    }
	
	//PUBLIC FUNCTION: addNewTeam
	//Add a new team
	public function addNewTeam(){
	
		//Grab the UID of the logged in user
		$userUID = $this->Auth->user('uid');
		
		//Create a new record in the database for that model
		$teamData = $this->Team->setupDefaultTeam( $userUID );
	
		//Set the variables
		$this->set( 'teamData',		$teamData );
		$this->set( 
			'_serialize', 
			array( 
				'teamData'
			) 
		);
		
	}
	
	//PUBLIC FUNCTION: changeTeamName
	//Change the name of the given team
	public function changeTeamName(){
	
		$jsonValues	= $this->params['url'];
		$teamName	= $jsonValues['teamName'];
		$teamUID	= $jsonValues['teamUID'];
	
		//Change the name of the given team
		$success = $this->Team->changeName( $teamUID, $teamName );
					
		//Set it on
		$this->set( 'success', $success );
		$this->set( '_serialize', array(
						'success'
					));
						
	}
	
	//PUBLIC FUNCTION: getUnitsInTeam
	//Return all the units in a given team
	public function getUnitsInTeam( $teamUID ){
	
		//Grab the UID of the logged in user
		$userUID = $this->Auth->user('uid');
		
		//Get all the teams associated with the user
		$teams = $this->Team->getTeamsByUserUID( $userUID );
		
		//Set the teams variable so that it can be passed to the 
		//view or JSON
		$this->set( 'teams', $teams );
		$this->set( 
			'_serialize', 
			array( 
				'teams'
			) 
		);
		
	}
	
	
	
}