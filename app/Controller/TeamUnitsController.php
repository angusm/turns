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
		
		$unitTypeUID 	= $jsonValues['unitTypeUID'];
		$teamUID 	 	= $jsonValues['teamUID'];
		$x			 	= $jsonValues['x'];
		$y				= $jsonValues['y'];
		
		//Call the model function
		$success = $this->TeamUnit->addUnitToTeamByUnitTypeUID( $unitTypeUID, $teamUID, $userUID );

		//If we had enough of the unit to add, add the unit to the given position
		if( $success != false ){
			$teamUnitPositionModelInstance = ClassRegistry::init( 'TeamUnitPosition' );
			$teamUnitPositionModelInstance->assignPosition( $success['TeamUnit']['uid'], $x, $y );
		}
			
		//Pass success to the view
		$this->set( 'success', 		$success 		);
		$this->set( 'unitTypeUID', 	$unitTypeUID 	);
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
		
		$teamUID 	 	= $jsonValues['teamUID'];
		$unitTypeUID 	= $jsonValues['unitTypeUID'];
		$x 				= $jsonValues['x'];
		$y				= $jsonValues['y'];
		
		//Before we can remove the unit from the TeamUnit model we need to
		//remove it from the TeamUnitPositions model, this is why we grabbed the
		//x and y from the jSON values
		$teamUnitPositionModelInstance = ClassRegistry::init('TeamUnitPosition');
		$teamUnitPositionModelInstance->removeTeamUnit( $teamUID, $unitTypeUID, $x, $y );
		
		//Call the model function
		$success = $this->TeamUnit->removeUnitFromTeamByUnitTypeUID( $unitTypeUID, $teamUID, $userUID );
			
		//Pass success to the view
		$this->set( 'success', 		$success 		);
		$this->set( 'unitTypeUID', 	$unitTypeUID 	);
		$this->set(	'x',			$x 				);
		$this->set( 'y',			$y				);
		$this->set(
				'_serialize',
				array(
					'success',
					'unitTypeUID',
					'x',
					'y'
				)
			);
		
	}
	
}