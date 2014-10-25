<?php
// app/Controller/UsersController.php
/**
 * Class UsersController
 * @property mixed User
 */
class UsersController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow( 'processLogin', 'register' );
    }

	//PUBLIC FUNCTION: index
	//Essentially a homepage for the users where they can
	//view all their lovely stuff.
    public function index() {
		
		//Empty... for now!
		
    }
	
	//PUBLIC FUNCTION: login
	public function login() {
		//If we're dealing with a posted message
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->redirect($this->Auth->redirectUrl());
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'));
			}
		}
	}


    //PUBLIC FUNCTION: processLogin
    //Designed to handle a login request generated through the javascript attached
    //to the user bar at the top of any page
    public function processLogin(){

        //Grab the necessary data from the JSON
        $jsonData = $this->params['url'];

        //Grab the user
        $user = $this->User->find( 'first', [
            'fields' => [
                'User.uid',
                'User.username'
            ],
            'conditions' => [
                'username'  => $jsonData['username'],
                'password' => Security::hash($jsonData['password'])
            ]
        ]);

        if( $user != false ){
            $this->Auth->login( $user['User'] );
            $success = true;
        }else{
            $success = false;
        }

        $this->set( 'redirectURL',  $this->Auth->redirectUrl()    );
        $this->set( 'success',      $success                   );
        $this->set( 'user',         $this->Auth->user()        );
        $this->set(	'_serialize', [
            'redirectURL',
            'success',
            'user'
        ]);

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

            if ( $successfulSave ) {

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
	
}