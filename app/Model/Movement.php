<?php
class Movement extends AppModel {

	public $hasMany = array(
							'MovementDirectionSet' => array(
								'className'		=> 'MovementDirectionSet',
								'foreignKey'	=> 'movements_uid'
							)
						);
						
	public $belongsTo = array( 
							'MovementSet' => array(
								'className'		=> 'MovementSet',
								'foreignKey'	=> 'movement_sets_uid'
							)
						);

	//PUBLIC FUNCTION: __construct
	//Override the constructor to setup some fun stuff
	public function __construct() { 
	
		//Call the parent constructor
		parent::__construct(); 

		//Call the parent function to setup the key validation for the relation			
		parent::setupUIDRelation( array( 'MovementSet' ) );
		
		$this->validate = array_merge( 
					array(
						'spaces' => array(
							'rule'    => 'numeric',
							'required' 	=>	true
						 ),
						'priority' => array(
							'rule'    => 'numeric',
							'required' 	=>	true
						 )
					),
					$this->validate
				);

	}
	
}

