<?php
// app/Controller/UsersController.php
class CardArtLayerSetsController extends AppController {

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
	
	//PUBLIC FUNCTION: register
	//Sets up the page where a user can register 
    public function add() {
		
		//If we're dealing with a posted message
        if ($this->request->is('post')) {
			
			//Create the user
            $this->CardArtLayerSet->create();
			
			//See if we can save the user using the given data...
			if ($this->CardArtLayerSet->save($this->request->data)) {
				//If the user has been saved, indicate as much and do a
				//redirect.
				$this->Session->setFlash(__('Card Art Layer Set Saved'));
				$this->redirect(['action' => 'index']);
			} else {
				//If the user couldn't be saved then indicate as much.
				$this->Session->setFlash(__('Couldn\'t save the art set, maybe the art sucks too hard?'));
			}
        }
    }
	
}