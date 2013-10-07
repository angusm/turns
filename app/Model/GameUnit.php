<?php
class GameUnit extends AppModel {

	//Setup the associations for this model
	public $belongsTo = array(
						'Game' => array(
							'className' 	=> 'Game',
							'foreignKey'	=> 'games_uid'
						),
						'TeamUnit' => array(
							'className' 	=> 'TeamUnit',
							'foreignKey'	=> 'team_units_uid'
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
	
	//PUBLIC FUNCTION: addToGameFromTeam
	//Take all the units in a given team and add them to a game
	public function addToGameFromTeam( $gameUID, $teamUID ){
	
		//Grab the team's units
		$teamUnitModelInstance = ClassRegistry::init( 'TeamUnit' );
		$teamUnits = $teamUnitModelInstance->getAllUnits( $teamUID );
		
		//Loop through all the units
		foreach( $teamUnits as $teamUnit ){
		
			//Setup the data
			$gameUnitData = array(
									'user_games_uid' 	=> $gameUID,
									'units_uid'			=> $teamUnit['TeamUnit']['units_uid'],
									'turn'				=> 1,
									'x'					=> -1,
									'y'					=> -1,
									'defense'			=> $teamUnit['Unit']['UnitType']['UnitStat']['defense']
								);
								
			//Create a new record for the unit and save it
			$this->create();
			$this->save( $gameUnitData );
											
		}
			
		
	}
	
}

