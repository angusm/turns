<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @property mixed params
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
	public $helpers = [ 'TurnForm', 'Html', 'Knockout'];
    public $components = [
        'Auth',
		'RequestHandler',
        'Session'
    ];

    //Before anything else happens, the beforeFilter happens
    public function beforeFilter() {

	    //Set the login action
        $this->Auth->loginAction = [
            'controller'    => 'users',
            'action'        => 'register',
            'plugin'        => null,
            '?'             => $this->params->query
        ];

        //Handle restrictions
        //$this->Auth->allow();

        //Check to see if we should be using the default layout or the none layout
        if(
            isset($this->params->query['requestType']) &&
            $this->params->query['requestType'] == 'content'
        ){
	        //Change the default layout
            $this->layout   = 'none';

	        //Set the default variables
	        $menuItems      = [];
        }else{
	        //Set the default variables for non-content loading
	        $menuItemModel  = ClassRegistry::init('MenuItem');

	        //Grab all of the menu items
	        $menuItems      = $menuItemModel->getAvailableMenuItems();
        }

	    //Pass to the view what needs to be passed
	    $this->set( 'authUser',     $this->Auth->user() );
	    $this->set( 'menuItems',    $menuItems );

    }

    //PROTECTED FUNCTION: getInstance
    //Return an instance of whatever model this is the controller for
	/**
	 * @return object
	 */
	protected function getInstance(){
	
		//Get the model name from the built in CakePHP function
		$modelName 		= Inflector::classify( $this->request->controller );
		//Create an instance of said model
		$modelInstance 	= ClassRegistry::init( $modelName );
		
		//Return the instance
		return $modelInstance;
		
	}
	
	//PUBLIC FUNCTION:getRecordData
	//Return all the relevant information about a record as a response to a JSON
	//request
	public function getRecordData(){
		
		//Grab the data we were sent
        $requestedUID = $this->params['url']['uid'];
                
		//Get the model we're dealing with
		$modelInstance 	= $this->getInstance();

		//Grab the requested record
		$requestedRecord = $modelInstance->find( 'first', [
								'conditions' => [
									'uid' => $requestedUID
								]
							]);

        //Get the model name
        $modelName 		= key($requestedRecord);

        //Return the given record
		$this->set( $modelName, $requestedRecord[$modelName] );
		$this->set( '_serialize', [
            $modelName
        ]);
		
		
	}
	
	//PUBLIC FUNCTION: index
	//Used to handle empty GET REST requests
	public function index(){
		
	}
	
	//PUBLIC FUNCTION: manage
	//Setup a screen to manage the data
	public function manage(){
		
		//Get the model we're dealing with
		$modelInstance 	= $this->getInstance();
		
		//Pass forward the structure, management list and model name
		$this->set( 'structure', $modelInstance->getStructure() );

		//Render the view
		$this->render('../App/manage');
		
	}
	
	//PUBLIC FUNCTION: newRecord
	//Create a new record of whatever model we're dealing with
	public function newRecord(){
		
		//Get the model name
		$modelName = Inflector::classify( $this->request->controller );
		
		//Get an instance of the model name we can work with
		$modelInstance	= ClassRegistry::init( $modelName );
		
		//Create a new record in the database for that model
		$nuUID = $modelInstance->createNewRecord();
	
		//Set the variables
		$this->set( 'modelName', 	$modelName );
		$this->set( 'name', 		'Default' );
		$this->set( 'uid', 			$nuUID );
		$this->set( 
			'_serialize', 
			[
				'modelName',
				'name',
				'uid'
			]
		);
		
         //Render the view
		$this->render('../App/newRecord');
		
	}
	
	//PUBLIC FUNCTION: remove
	//Delete the record with the given UID
	public function remove(){
		
		//Grab that sweet sweet json data
		$jsonValues = $this->params['url'];
		
		//Grab the uid
		$uid = $jsonValues['uid'];
		
		//Get the model name
		$modelName = Inflector::classify( $this->request->controller );
		
		//Get an instance of the model name we can work with
		$modelInstance	= ClassRegistry::init( $modelName );
		$success = $modelInstance->remove( $uid, true );
		
		$this->set( 'success', $success );
		$this->set( '_serialize', [
						'success'
					]);
		
	}
        
	//PUBLIC FUNCTION: saveFormData
	//Take in post data from a javascript call and then write it to the file
	public function saveFormData(){
		
		//Grab the JSON values
		$jsonValues = $this->params['url'];
	
		//Get an instance of the model name we can work with
		$modelInstance	= $this->getInstance();
		
		//Call the JSON array
		$success    = $modelInstance->saveWithJSONFormData( $jsonValues );
        reset($success);
		$modelName  = key($success);

		$this->set( $modelName, $success[$modelName] );
		$this->set(
			'_serialize', 
			[
				$modelName
			]
		);
		
		$this->render('../App/default');
            
    }

    //PUBLIC FUNCTION: viewManagementList
    //Used to create a nice table view of all of the records contained in
    //the model. Management functions and editing opportunities should
    //appear also
    public function viewManagementList(){

        //To create the management list we need to setup the parameters
        //for the display. Then the view will have to contain the javascript
        //to make the queries for extra data. We can't fit something like
        //the history of ordered items into a list without dynamic loading,
        //the server would die

        //Start by grabbing the model name and instance

        //Get the model name
        $modelName = Inflector::classify( $this->request->controller );

        //Get an instance of the model name we can work with
        $modelInstance	= ClassRegistry::init( $modelName );

        //Also, while we're at it, we get a friendly list of model names if possible
        $friendlyColumnNameModelInstance = ClassRegistry::init( 'FriendlyColumnName' );

        //Now we grab the schema for the model and we look for a friendly column name for
        //each column
        $schema = $modelInstance->schema();
        $friendlyColumnNameModelInstance->makeSchemaFriendly( $schema, $modelName );

        //To establish
        $this->set( 'schema', $schema );

        //Render the default view
        $this->render('../App/view_management_list');

    }
	
}




