<?php
// app/Controller/DirectionSetsController.php
/**
 * Class DirectionSetDirectionsController
 * @property mixed DirectionSetDirection
 */
class DirectionSetDirectionsController extends AppController {

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
	//Used to create a new direction set
	public function add() {
		
		//If we're dealing with a posted message
        if ($this->request->is('post')) {
			
			//Create the user
            $this->DirectionSetDirection->create();
			
			//See if we can save the user using the given data...
			if ($this->DirectionSetDirection->save($this->request->data)) {
				//If the user has been saved, indicate as much and do a
				//redirect.
				$this->Session->setFlash(__('Direction Set Saved'));
				$this->redirect(['action' => 'index']);
			} else {
				//If the user couldn't be saved then indicate as much.
				$this->Session->setFlash(__('AAAAAaaaaa! WRONG!'));
			}
        }
    }
	
}