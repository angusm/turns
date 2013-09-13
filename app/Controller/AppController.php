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
        'Auth' => array(
            'loginRedirect' => array('controller' => 'posts', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'pages', 'action' => 'display', 'home')
        ),
		'RequestHandler',
        'Session'
    );

    public function beforeFilter() {
        $this->Auth->allow('index', 'view');
    }
	
	//PUBLIC FUNCTION: add
	//Default route of a POST REST request
	//We don't want add to do anything by default so in this appModel we're
	//leaving it empty
	public function add(){		
	}
	
	//PUBLIC FUNCTION: delete
	//Default route of a DELETE REST request
	//Leaving this the hell alone as it'd be hella dangerous to have behaviour
	//defined in the AppModel
	public function delete( $uid ){
		
	}
	
	//PUBLIC FUNCTION: edit
	//Default route of a PUT/POST REST request
	//We leave that shit the hell alone in this default REST model
	public function edit( $uid ){
		
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
	
	//PUBLIC FUNCTION: index
	//Used to handle empty GET REST requests
	public function index(){
		
	}
	
	//PUBLIC FUNCTION: manage
	//Setup a screen to manage the data
	public function manage(){
		
		//Get the model we're dealing with
		$modelInstance 	= $this->getInstance();
		
		//Get the model name
		$modelName 		= Inflector::classify( $this->request->controller );
		
		//Pass forward the structure, management list and model name
		$this->set( 'managementList',	$modelInstance->getManagementList()	);
		$this->set( 'structure', 		$modelInstance->getStructure() 		);
		$this->set( 'modelName',		$modelName							);
		
		//Render the view
		$this->render('..\App\manage');
		
	}
	
	//PUBLIC FUNCTION: newRecord
	//Create a new record of whatever model we're dealing with
	public function newRecord(){
		
		//Get an instance of the model name we can work with
		$modelInstance	= ClassRegistry::init( $modelName );
		
		//Create a new record in the database for that model
		$nuUID = $modelInstance->createNewRecord();
	
		$this->set( 'nuUID', $nuUID );
		$this->set( '_serialize', array( 'nuUID' ) );
		
	}
	
	//PUBLIC FUNCTION: view
	//Used to handle GET requests that specify a UID
	public function view( $uid ){
		
	}
	
}




