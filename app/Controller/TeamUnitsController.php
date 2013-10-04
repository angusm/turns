<?php
// app/Controller/DirectionSetsController.php
class TeamUnitsController extends AppController {

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
	
	//PUBLIC FUNCTION: addUnitToTeamByUnitTypeUID
	//Take the given Unit Type UID and add one of the player's units of that type to
	//the given team
	public function addUnitToTeamByUnitTypeUID(){
			
		//Grab the UID of the logged in user
		$userUID = $this->Auth->user('uid');
		
		//Grab the jSON values
		$jsonValues = $this->params['url'];
		
		$unitTypeUID = $jsonValues['unitTypeUID'];
		$teamUID 	 = $jsonValues['teamUID'];
		
		//Call the model function
		$success = $this->TeamUnit->addUnitToTeamByUnitTypeUID( $unitTypeUID, $teamUID, $userUID );
			
		//Pass success to the view
		$this->set( 'success', $success );
		$this->set( 'unitTypeUID', $unitTypeUID );
		$this->set(
				'_serialize',
				array(
					'success',
					'unitTypeUID'
				)
			);
			
	}
			
	//PUBLIC FUNCTION: getUnitsOnTeam
	//Return the units on the team
	public function getUnitsOnTeam() {
		
		//Grab the JSON values
		$jsonValues = $this->params['url'];
		
		//Grab the teamUID from the JSON values
		$teamUID 	= $jsonValues['teamUID'];
		
		//Grab the units
		$unitsOnTeam = $this->TeamUnit->getUnitsOnTeam( $teamUID );
		
		//Pass them to the view
		$this->set( 'unitsOnTeam', $unitsOnTeam );
		$this->set(
			'_serialize',
			array(
				'unitsOnTeam'
			)
		);
		
	}
	
	//PUBLIC FUNCTION: removeUnitFromTeamByUnitTypeUID
	//Remove a unit with the given Unit Type UID from the given team
	public function removeUnitFromTeamByUnitTypeUID(){
			
		//Grab the UID of the logged in user
		$userUID = $this->Auth->user('uid');
		
		//Grab the jSON values
		$jsonValues = $this->params['url'];
		
		$unitTypeUID = $jsonValues['unitTypeUID'];
		$teamUID 	 = $jsonValues['teamUID'];
		
		//Call the model function
		$success = $this->TeamUnit->removeUnitFromTeamByUnitTypeUID( $unitTypeUID, $teamUID, $userUID );
			
		//Pass success to the view
		$this->set( 'success', $success );
		$this->set( 'unitTypeUID', $unitTypeUID );
		$this->set(
				'_serialize',
				array(
					'success',
					'unitTypeUID'
				)
			);
		
	}
	
}