<?php

/**
 * Class UnitArtSet
 */
class UnitArtSet extends AppModel {

		public $hasMany = [
							'CardArtLayerSet' => [
								'className' 	=> 'CardArtLayerSet',
								'foreignKey'	=> 'unit_art_sets_uid'
							],
							'UnitArtSetIcon'	=> [
								'className'		=> 'UnitArtSetIcon',
								'foreignKey'	=> 'unit_art_sets_uid'
							],
                            'GameUnit' => [
                                'className'     => 'GameUnit',
                                'foreignKey'    => 'unit_art_sets_uid'
                            ],
                            'UnitType' => [
                                'className'     => 'UnitType',
                                'foreignKey'    => 'unit_art_sets_uid'
                            ]
						];

		//Override the constructor so that we can set the variables our way
		//and not some punk ass way we don't much like.
	/**
	 *
	 */
	public function __construct() {
	
			//Call the parent constructor
			parent::__construct();
			
			$this->validate = array_merge( 
						[
							'name' => [
								'rule'    	=> 'alphaNumeric',
								'required' 	=>	true,
								'message' 	=> 	parent::$alphaNumericMessage
							 ]
						],
						$this->validate
					);

		}
		
		//PUBLIC FUNCTION: getDefaultArtByUnitTypeUID
		//Grab the default art set for the given Unit Type UID 
		//We start by looking for the art set named default with the given Unit
		//Type UID, but if we can't find one then we just grab the first one we
		//can find. But if that's the case then someone fucked up, every unit should
		//have a default art set.
	/**
	 * @param $unitTypeUID
	 * @return array
	 */
	public function getDefaultArtByUnitTypeUID( $unitTypeUID ){
		
			//Start by trying for a set named default with the Unit Type UID
			$defaultArtSet = $this->find( 'first', [
											'conditions' => [
												'unit_types_uid' => $unitTypeUID,
												'name' => 'Default'
											]
										]);
			
			//If the default set wasn't found then just grab the first record with the
			//right unit type
			if( $defaultArtSet == false ){
			
				$defaultArtSet = $this->find( 'first', [
												'conditions' => [
													'unit_types_uid' => $unitTypeUID
												]
											]);
				
			}

			//Return the default art set if we found one or false if we didn't
			return $defaultArtSet;
			
		}
	
}

