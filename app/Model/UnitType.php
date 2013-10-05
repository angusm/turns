<?php
class UnitType extends AppModel {
	
	//Setup the associations for UnitType
	public $hasMany = array(
						'Unit' => array(
							'className' 	=> 'Unit',
							'foreignKey'	=> 'unit_types_uid'
						),
						'UnitArtSet'	=> array(
							'className'		=> 'UnitArtSet',
							'foreignKey'	=> 'unit_types_uid'
						)
						
					);
	
	//NOTE: Problem with belongs to not fetching its has many associations in a
	//containable array. So a finderQuery is needed.
	public $belongsTo = array(
							'UnitStat' => array(
								'className'		=> 'UnitStat',
								'foreignKey'	=> 'unit_stats_uid',
								'finderQuery'	=> 'SELECT UnitStat.* FROM unit_types, unit_stats AS UnitStat WHERE unit_types.uid={$__cakeID__$} AND unit_types.unit_stats_uid=UnitStat.uid'
	
							)
						);


	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 
			parent::__construct(); 
			
		//Setup rules that apply to all attributes
		$this->attributeRules = array(
			'rule'		=> 'numeric',
			'required'	=> true,
			'message'	=> 'Attributes must be INT, we\'re not making a math game here'
		);

		//Setup the validation
		$this->validate = array(
			'name' => array(
				'default'	=> 	'Default',
				'message' 	=> 	parent::$alphaNumericMessage,
				'required' 	=>	true,
				'rule'		=> 	'alphaNumeric'
			),
			'unit_stats_uid' => array(
				'default'	=> '1',
				'message'	=> 'Need a Unit Stats UID',
				'required'	=> true,
				'rule'		=> 'numeric'
			)
		);

	}
	
	//PUBLIC FUNCTION: decrementTicket
	//For the given UnitType UID drop the ticket count for that unit by one
	public function decrementTicket( $unitTypeUID ){
		
		//Grab the u nit type we'll be decrementing
		$unitToDecrement = $this->find( 'first', array(
								'conditions' => array(
									'UnitType.uid' => $unitTypeUID
								)
							));
	
		//Decrement it
		if( $unitToDecrement != false ){
			$newTicketCount = $unitToDecrement['UnitType']['remaining_rare_tickets'] - 1;
		
			$this->read( null, $unitToDecrement['UnitType']['uid'] );
			$this->set( 'remaining_rare_tickets', $newTicketCount );
			$this->save();
		}
		
	}
	
	//PUBLIC FUNCTION: findByUID
	//Given a UID for a Unit Type return the corresponding record
	public function findByUID( $uid ){
	
		//Run the find and return it
		return $this->find( 'first', array(
								'conditions' => array(
									'uid' => $uid
								)								
							));
		
	}
	
	//PUBLIC FUNCTION: getCardViewData
	//Get all the information necessary to display a card view
	public function getCardViewData( $uid ){
		
		$cardViewData = $this->find( 'first', array(
								'conditions' => array(
									'UnitType.uid'	=> $uid
								),
								
								'contain' => array(
									'UnitStat'	=> array(
									
										'fields' => array(
											'UnitStat.uid',
											'UnitStat.name',
											'UnitStat.damage',
											'UnitStat.defense',
											'UnitStat.teamcost'
										),
										
										'UnitStatMovementSet.MovementSet' => array(
	
											  'fields' => array(
												  'MovementSet.name',
												  'MovementSet.uid'
											  ),
  
											  'Movement' => array(
  
												  'fields' => array(
													  'Movement.spaces',
													  'Movement.priority'
												  ),
  
												  'MovementDirectionSet.DirectionSet.'.
												  'DirectionSetDirection.Direction' => array(
													  'fields' => array(
														  'Direction.x',
														  'Direction.y',
														  'Direction.name'
													  )
												  )
											  )
											  
										)
										
									),
									'UnitArtSet' => array(
										
										'fields' => array(
											'UnitArtSet.name',
										),
										
										'CardArtLayerSet' => array(
											
											'fields' => array(
												'CardArtLayerSet.position'
											),
											
											'order'  => array(
												'CardArtLayerSet.position'
											),
											
											'CardArtLayer' => array(
											
												'fields' => array(
													'CardArtLayer.image'
												)
											)
										),
										
										'UnitArtSetIcon.Icon' => array(
												
											'fields' => array(
												'Icon.image',
												'Icon.icon_positions_uid'
											)
										)
										
									)
								),
								'fields' => array(
									'UnitType.name',
								)
							));

			return $cardViewData;
		
	}
	
	//PUBLIC FUNCTION: getRandomUnitTypeByTicket
	//Use the rarity ticket system to select a random unit
	public function getRandomUnitTypeByTicket(){
		
		//Grab all of the Unit Types that still have tickets remaining
		$eligibleUnits = $this->find( 'list', array(
										'conditions' => array(
											'remaining_rare_tickets >' => '0'
										),
										'fields' => array(
											'uid'
										)
									));
									
		//Grab the random UID and return it
		$foundUnitTypeUID = $eligibleUnits[array_rand( $eligibleUnits )];
		return $foundUnitTypeUID;
		
	}
	
	//PUBLIC FUNCTION: getUIDs
	//Return a list of all the UIDs
	public function getUIDs(){
	
		return $this->find( 'list', array(
				'fields' =>  'UnitType.uid'		
			));	
		
	}
	
	//PUBLIC FUNCTION: validateUnitType
	//Run a series of checks on the given Unit Type to ensure that it's ready
	//to be used and played with.
	//This checks for:
	//	- Valid Statistics
	//  - Valid Artset
	public function validateUnitType( $unitTypeUID ){
		
		//Assume we have a valid UnitType and then run through checks trying
		//to disprove it. Innocent until proven guilty
		$unitTypeIsValid = true;
		
		//First off we should make sure that such a Unit Type actually exists
		$unitTypeRecord = $this->find( 'first', array(
											'conditions' => array(
												'UnitType.uid' => $unitTypeUID
											),
											'contain' => array(
												'UnitStat',
												'UnitArtSet'
											)
										));

		//If the given UID was invalid we return false
		if( $unitTypeRecord == false ){
			return false;
		}
		
		//If we don't have a valid set of stats, return false
		if( ! isset( $unitTypeRecord['UnitStat']['uid'] ) || ! count( $unitTypeRecord['UnitStat'] ) > 0 ){
			return false;
		}
		
		//If we don't have a valid art set, return false
		if( ! isset( $unitTypeRecord['UnitArtSet'][0]['uid'] ) || ! count( $unitTypeRecord['UnitArtSet'] ) > 0 ){
			return false;
		}		
		
		//Return the validity of the Unit Type
		return $unitTypeIsValid;
		
	}
	
}

