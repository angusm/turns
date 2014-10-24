<?php
// app/Controller/UsersController.php
/**
 * Class UnitTypesController
 */
class UnitTypesController extends AppController {

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
	//Sets up the page where a user can register 
    public function add() {
		
		//If we're dealing with a posted message
        if ($this->request->is('post')) {
			
			//Create the user
            $this->UnitType->create();
			
			//See if we can save the user using the given data...
			if ($this->UnitType->save($this->request->data)) {
				//If the user has been saved, indicate as much and do a
				//redirect.
				$this->Session->setFlash(__('Unit Type Saved'));
				$this->redirect(['action' => 'index']);
			} else {
				//If the user couldn't be saved then indicate as much.
				$this->Session->setFlash(__('Couldn\'t save that unit type, it\'s dead now.'));
			}
        }
    }

    //PUBLIC FUNCTION: getCardViewData
    //Return the card view data for a given unit
    public function getCardViewData(){

        //Grab the parameters
        $jsonData = $this->params['url'];

        //Get the data from the model
        $cardViewData = $this->UnitType->getCardViewData(
            $jsonData['unitTypeUID']
        );

        //Pass it back
        $this->set( 'cardViewData', $cardViewData );
        $this->set( '_serialize', [
            'cardViewData'
        ]);

    }

    //PUBLIC FUNCTION: manage
    //Setup a screen to manage the data
    public function manage(){

        //Pass forward the structure, management list and model name
        $this->set(
            'structure',
            $this->UnitType->getStructure(
                [],
                [],
                ['GameUnitType', 'GameUnit', 'Unit']
            )
        );

        //Render the view
        $this->render('../App/manage');

    }
	
	
}