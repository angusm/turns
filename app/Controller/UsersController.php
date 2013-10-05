<?php
// app/Controller/UsersController.php
class UsersController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('register');
    }

	//PUBLIC FUNCTION: index
	//Essentially a homepage for the users where they can
	//view all their lovely stuff.
    public function index() {
		
		//Empty... for now!
		
    }
	
	//PUBLIC FUNCTION: register
	//Sets up the page where a user can register 
    public function register() {
		
		//If we're dealing with a posted message
        if ($this->request->is('post')) {
			
			//Create the user
            $this->User->create();
			
			//See if we can save the user using the given data...
			$successfulSave = $this->User->save($this->request->data);
			
			$userUID = $this->User->id;
			if ( $successfulSave ) {
				
				//If we saved the user we better be damn sure to initialize
				//them
				$this->User->setupNewUser( $userUID );
				
				//If the user has been saved, indicate as much and do a
				//redirect.
				$this->Session->setFlash(__('Thank you for registering.'));
				//$this->redirect(array('action' => 'index'));
				
			} else {
				//If the user couldn't be saved then indicate as much.
				$this->Session->setFlash(__('We were unable to register your account. Plase, try again.'));
			}
        }
    }
	
	
	public function login() {
		//If we're dealing with a posted message
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'));
			}
		}
	}
}