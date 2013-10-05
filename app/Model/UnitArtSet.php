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
		
		//PUBLIC FUNCTION: getDefaultArtByUnitTypeUID
		//Grab the default art set for the given Unit Type UID 
		//We start by looking for the art set named default with the given Unit
		//Type UID, but if we can't find one then we just grab the first one we
		//can find. But if that's the case then someone fucked up, every unit should
		//have a default art set.
		public function getDefaultArtByUnitTypeUID( $unitTypeUID ){
		
			//Start by trying for a set named default with the Unit Type UID
			$defaultArtSet = $this->find( 'first', array(
											'conditions' => array(
												'unit_types_uid' => $unitTypeUID,
												'name' => 'Default'
											)
										));
			
			//If the default set wasn't found then just grab the first record with the
			//right unit type
			if( $defaultArtSet == false ){
			
				$defaultArtSet = $this->find( 'first', array(
												'conditions' => array(
													'unit_types_uid' => $unitTypeUID
												)
											));	
				
			}

			//Return the default art set if we found one or false if we didn't
			return $defaultArtSet;
			
		}
	
}

