<?php
// app/Controller/UsersController.php
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
		
		//Grab all of the units for this user
		$units = $this->Unit->getUnitsForUserByUID( $userUID );
		
		//Grab the unit list for this user
		$unitList = $this->Unit->getUnitListForUserByUID( $userUID );
		
		//Pass the units on to the screen
		$this->set( 'units',	$unitList );
		
	}
	
}