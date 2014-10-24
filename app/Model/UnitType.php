<?php

/**
 * Class UnitType
 */
class UnitType extends AppModel {
	
	//Setup the associations for UnitType
	public $hasMany = [
						'Unit' => [
							'className' 	=> 'Unit',
							'foreignKey'	=> 'unit_types_uid'
						]
					];
	
	//NOTE: Problem with belongs to not fetching its has many associations in a
	//containable array. So a finderQuery is needed.
	public $belongsTo = [
						'UnitArtSet'	=> [
							'className'		=> 'UnitArtSet',
							'foreignKey'	=> 'unit_art_sets_uid',
							'finderQuery'	=> 'SELECT UnitArtSet.* FROM unit_types, unit_art_sets AS UnitArtSet WHERE unit_types.uid={$__cakeID__$} AND unit_types.unit_art_sets_uid=UnitArtSet.uid'
						],
						'UnitStat' => [
							'className'		=> 'UnitStat',
							'foreignKey'	=> 'unit_stats_uid',
							'finderQuery'	=> 'SELECT UnitStat.* FROM unit_types, unit_stats AS UnitStat WHERE unit_types.uid={$__cakeID__$} AND unit_types.unit_stats_uid=UnitStat.uid'

						],
					];


	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	/**
	 *
	 */
	public function __construct() {
			parent::__construct(); 
			
		//Setup rules that apply to all attributes
		$this->attributeRules = [
			'rule'		=> 'numeric',
			'required'	=> true,
			'message'	=> 'Attributes must be INT, we\'re not making a math game here'
		];

		//Setup the validation
		$this->validate = [
			'name' => [
				'default'	=> 	'Default',
				'message' 	=> 	parent::$alphaNumericMessage,
				'required' 	=>	true,
				'rule'		=> 	'alphaNumeric'
			],
			'unit_stats_uid' => [
				'default'	=> '1',
				'message'	=> 'Need a Unit Stats UID',
				'required'	=> true,
				'rule'		=> 'numeric'
			]
		];

	}
	
	//PUBLIC FUNCTION: decrementTicket
	//For the given UnitType UID drop the ticket count for that unit by one
	/**
	 * @param $unitTypeUID
	 */
	public function decrementTicket( $unitTypeUID ){
		
		//Grab the u nit type we'll be decrementing
		$unitToDecrement = $this->find( 'first', [
								'conditions' => [
									'UnitType.uid' => $unitTypeUID
								]
							]);
	
		//Decrement it
		if( $unitToDecrement != false ){
			$newTicketCount = $unitToDecrement['UnitType']['remaining_rare_tickets'] - 1;
		
			$this->read( null, $unitToDecrement['UnitType']['uid'] );
			$this->saveField( 'remaining_rare_tickets', $newTicketCount );
			
		}
		
	}
	
	//PUBLIC FUNCTION: findByUID
	//Given a UID for a Unit Type return the corresponding record
	/**
	 * @param $uid
	 * @return array
	 */
	public function findByUID( $uid ){
	
		//Run the find and return it
		return $this->find( 'first', [
								'conditions' => [
									'uid' => $uid
								]
							]);
		
	}
	
	//PUBLIC FUNCTION: getCardViewData
	//Get all the information necessary to display a card view
	/**
	 * @param $uid
	 * @return array
	 */
	public function getCardViewData( $uid ){
		
		$cardViewData = $this->find( 'first', [
								'conditions' => [
									'UnitType.uid'	=> $uid
								],
								
								'contain' => [
									'UnitStat'	=> [
									
										'fields' => [
											'UnitStat.uid',
											'UnitStat.name',
											'UnitStat.damage',
											'UnitStat.defense',
											'UnitStat.teamcost'
										],
										
										'UnitStatMovementSet.MovementSet' => [
	
											  'fields' => [
												  'MovementSet.name',
												  'MovementSet.uid'
											  ],
  
											  'Movement' => [
  
												  'fields' => [
													  'Movement.spaces',
													  'Movement.priority'
												  ],
  
												  'MovementDirectionSet.DirectionSet.'.
												  'DirectionSetDirection.Direction' => [
													  'fields' => [
														  'Direction.x',
														  'Direction.y',
														  'Direction.name'
													  ]
												  ]
											  ]
											  
										]
										
									],
									'UnitArtSet' => [
										
										'fields' => [
											'UnitArtSet.name',
										],
										
										'CardArtLayerSet' => [
											
											'fields' => [
												'CardArtLayerSet.position'
											],
											
											'order'  => [
												'CardArtLayerSet.position'
											],
											
											'CardArtLayer' => [
											
												'fields' => [
													'CardArtLayer.image'
												]
											]
										],
										
										'UnitArtSetIcon.Icon' => [
												
											'fields' => [
												'Icon.image',
												'Icon.icon_positions_uid'
											]
										]
										
									]
								],
								'fields' => [
									'UnitType.name'
								]
							]);

			return $cardViewData;
		
	}
	
	//PUBLIC FUNCTION: getRandomUnitTypeByTicket
	//Use the rarity ticket system to select a random unit
	/**
	 * @return mixed
	 */
	public function getRandomUnitTypeByTicket(){
		
		//Grab a random Unit Type that still has tickets remaining
		$eligibleUnit = $this->find( 'first', [
										'conditions' => [
											'remaining_rare_tickets >' => '0'
										],
										'fields' => [
											'uid'
										],
                                        'order' => 'rand()'
									]);

		//Grab the random UID and return it
		return $eligibleUnit['UnitType']['uid'];
		
	}
	
	//PUBLIC FUNCTION: getUIDs
	//Return a list of all the UIDs
	/**
	 * @return array
	 */
	public function getUIDs(){
	
		return $this->find( 'list', [
				'fields' =>  'UnitType.uid'		
			]);
		
	}
	
	//PUBLIC FUNCTION: validateUnitType
	//Run a series of checks on the given Unit Type to ensure that it's ready
	//to be used and played with.
	//This checks for:
	//	- Valid Statistics
	//  - Valid Artset
	/**
	 * @param $unitTypeUID
	 * @return bool
	 */
	public function validateUnitType( $unitTypeUID ){
		
		//Assume we have a valid UnitType and then run through checks trying
		//to disprove it. Innocent until proven guilty
		$unitTypeIsValid = true;
		
		//First off we should make sure that such a Unit Type actually exists
		$unitTypeRecord = $this->find( 'first', [
                                            'fields' => [
                                                'UnitType.uid'
                                            ],
											'conditions' => [
												'UnitType.uid' => $unitTypeUID
											],
											'contain' => [
												'UnitStat' => [
                                                    'fields' => [
                                                        'UnitStat.uid'
                                                    ]
                                                ],
												'UnitArtSet' => [
                                                    'fields' => [
                                                        'UnitArtSet.uid'
                                                    ]
                                                ]
											]
										]);

		//If the given UID was invalid we return false
		if( $unitTypeRecord == false ){
			return false;
		}
		
		//If we don't have a valid set of stats, return false
		if( ! isset( $unitTypeRecord['UnitStat']['uid'] ) || ! count( $unitTypeRecord['UnitStat'] ) > 0 ){
			return false;
		}
		
		//If we don't have a valid art set, return false
		if( ! isset( $unitTypeRecord['UnitArtSet']['uid'] ) || ! count( $unitTypeRecord['UnitArtSet'] ) > 0 ){
			return false;
		}		
		
		//Return the validity of the Unit Type
		return $unitTypeIsValid;
		
	}
	
}

