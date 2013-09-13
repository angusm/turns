<?php
class UnitTypeMovementSet extends AppModel {

	public $belongsTo = array(
							'MovementSet' => array(
								'className'		=> 'MovementSet',
								'foreignKey'	=> 'movement_sets_uid'
							)
						);	
	
		public function __construct() { 
            parent::__construct(); 

			//Call the parent function to setup the key validation for the
			//relation			
			parent::setupUIDRelation( array( 'MovementSet', 'UnitType' ) );

		}
	
}

