<?php
// app/Controller/UsersController.php
/**
 * Class UnitsController
 * @property mixed Unit
 */
class UnitsController extends AppController {

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
	
	//PUBLIC FUNCTION: manageUnits
	//Allow users to manage the units they have and their placement on teams
	public function manageUnits(){
	
		//Grab the UID of the logged in user
		$userUID = $this->Auth->user('uid');
		
		
		//Grab the unit list for this user
		$unitList = $this->Unit->getUnitListForUserByUID( $userUID );
		
		
		//Setup a team model and grab all the teams for this user
		$teamModelInstance = ClassRegistry::init( 'Team' );
		$teamList = $teamModelInstance->getTeamsByUserUID( $userUID );
				
		
		//Pass the lists onto the view
		$this->set( 'teamList', $teamList );
		$this->set( 'unitList',	$unitList );
		
	}
	
}