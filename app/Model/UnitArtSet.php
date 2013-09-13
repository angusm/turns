<?php
class UnitArtSet extends AppModel {

		public $hasMany = array(
							'CardArtLayerSet' => array(
								'className' 	=> 'CardArtLayerSet',
								'foreignKey'	=> 'unit_art_sets_uid'
							),
							'UnitArtSetIcon'	=> array(
								'className'		=> 'UnitArtSetIcon',
								'foreignKey'	=> 'unit_art_sets_uid'
							)
						);

		//Override the constructor so that we can set the variables our way
		//and not some punk ass way we don't much like.
		public function __construct() { 
	
			//Call the parent constructor
			parent::__construct(); 
	
			//Call the parent function to setup the key validation for the relation			
			parent::setupUIDRelation( array( 'UnitType' ) );
			
			$this->validate = array_merge( 
						array(
							'name' => array(
								'rule'    	=> 'alphaNumeric',
								'required' 	=>	true,
								'message' 	=> 	parent::$alphaNumericMessage
							 )
						),
						$this->validate
					);

		}
	
}

