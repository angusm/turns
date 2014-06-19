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
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
	public $helpers = array( 'TurnForm', 'Html' );
    public $components = array(
        'Auth',
		'RequestHandler',
        'Session'
    );

    //Before anything else happens, the beforeFilter happens
    public function beforeFilter() {

        $this->Auth->loginAction = array(
            'controller'    => 'users',
            'action'        => 'register',
            'plugin'        => null,
            '?'             => $this->params->query
        );
        $this->set( 'authUser', $this->Auth->user() );

        //Handle restrictions
        //$this->Auth->allow();

        //Check to see if we should be using the default layout or the none layout
        if(
            isset($this->params->query['requestType']) &&
            $this->params->query['requestType'] == 'content'
        ){
            $this->layout = 'none';
        }

    }

    //PROTECTED FUNCTION: getInstance
    //Return an instance of whatever model this is the controller for
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
		
		//Get the model name
		$modelName 		= Inflector::classify( $this->request->controller );
		
		//Grab the requested record
		$requestedRecord = $modelInstance->find( 'first', array(
								'conditions' => array(
									'uid' => $requestedUID
								)
							));
		
		//Setup an array of the values we'll want to serialize for JSON
		$serializableVariables = array();
		
		//Loop through all the information returned from the requested record
		//And throw it into the view and serializable variables
		foreach( $requestedRecord[$modelName] as $fieldName => $value ){
		
			$this->set( $fieldName, $value );
			$serializableVariables[] = $fieldName;
			
		}
		
		//Finally we have to let the calling function know what model name 
		//we're dealing with so make sure we set that up too
		$this->set( 'modelName',	$modelName );
		$serializableVariables[] = 'modelName';
		
		//Now serialize everything
		$this->set( '_serialize', $serializableVariables );
		
		
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
			array( 
				'modelName',
				'name',
				'uid'
			) 
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
		$this->set( '_serialize', array(
						'success'
					));
		
	}
        
	//PUBLIC FUNCTION: saveFormData
	//Take in post data from a javascript call and then write it to the file
	public function saveFormData(){
		
		//Grab the JSON values
		$jsonValues = $this->params['url'];
	
		//Get the model name
		$modelName = Inflector::classify( $this->request->controller );
	
		//Get an instance of the model name we can work with
		$modelInstance	= ClassRegistry::init( $modelName );
		
		//Call the JSON array
		$success = $modelInstance->saveWithJSONFormData( $jsonValues );
		
		$this->set( 'success', $success );
		$this->set( 'jsonValues', $jsonValues );
		$this->set( 
			'_serialize', 
			array( 
				'success',
				'jsonValues'
			) 
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




