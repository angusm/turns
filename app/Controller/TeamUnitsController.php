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
	
}