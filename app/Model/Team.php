<?php
class Team extends AppModel {

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 

		//Call the parent constructor
		parent::__construct(); 

		//Call the parent function to setup the key validation for the relation			
		parent::setupUIDRelation( array( 'User' ) );
		
		$this->validate = array_merge( 
					array(
						'name' => array(
							'rule'		=> 'alphaNumeric',
							'required' 	=>	true,
							'message'	=> 	parent::$alphaNumericMessage
						 )
					),
					$this->validate
				);
		
		

	}
	
	//PUBLIC FUNCTION: getTeamsByUserUID
	//Return all the teams owned by a given user
	public function getTeamsByUserUID( $userUID ){
	
		//Run the find
		$teams = $this->find( 'all', array(
						'conditions'	=> array(
							'Team.users_uid' => $userUID,
						)
					));
					
		//Return the teams
		return $teams;
		
	}
	
}

