<?php

/**
 * Class Team
 */
class Team extends AppModel {
	
	//Setup the association for this class
	public $belongsTo = [
							'User' => [
								'className' 	=> 'User',
								'foreignKey'	=> 'users_uid'
							]
						];

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	/**
	 *
	 */
	public function __construct() {

		//Call the parent constructor
		parent::__construct();
		
		$this->validate = array_merge( 
					[
						'name' => [
							'rule'		=> 'alphaNumeric',
							'required' 	=>	true,
							'message'	=> 	parent::$alphaNumericMessage
						 ]
					],
					$this->validate
				);

	}
	
	//PUBLIC FUNCTION: changeName
	//Change the name of the given team
	/**
	 * @param $uid
	 * @param $nuName
	 * @return mixed
	 */
	public function changeName( $uid, $nuName ){
	
		$this->read( null, $uid );
		$this->set( 'name', $nuName );
		return $this->save();	
		
	}
	
	//PUBLIC FUNCTION: getTeamsByUserUID
	//Return all the teams owned by a given user
	/**
	 * @param $userUID
	 * @return array
	 */
	public function getTeamsByUserUID( $userUID ){
	
		//Run the find
		$teams = $this->find( 'all', [
						'conditions'	=> [
							'Team.users_uid' => $userUID,
						]
					]);
					
		//Return the teams
		return $teams;
		
	}
	
	//PUBLIC FUNCTION: setupDefaultTeam
	//Setup a default team for the given user
	/**
	 * @param $userUID
	 * @return mixed
	 */
	public function setupDefaultTeam( $userUID ){
	
		$this->create();
		$this->set( 'name', 'Default' );
		$this->set( 'users_uid', $userUID );
		return $this->save();
		
	}
	
}

