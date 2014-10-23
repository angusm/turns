<?php
// app/Controller/UsersController.php
class CardArtLayerMovementsController extends AppController {

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
            $this->CardArtLayerMovement->create();
			
			//See if we can save the user using the given data...
			if ($this->CardArtLayerMovement->save($this->request->data)) {
				//If the user has been saved, indicate as much and do a
				//redirect.
				$this->Session->setFlash(__('Card Art Layer Movement Saved'));
				$this->redirect(['action' => 'index']);
			} else {
				//If the user couldn't be saved then indicate as much.
				$this->Session->setFlash(__('Couldn\'t save the art layer movement, probably too vigorous.'));
			}
        }
    }
	
}