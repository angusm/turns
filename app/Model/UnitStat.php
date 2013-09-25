<?php
class UnitStat extends AppModel {
	
	//Setup the associations for UnitType
	/*public $hasMany = array(
						'UnitType' => array(
							'className' 	=> 'Unit',
							'foreignKey'	=> 'unit_stats_uid'
						)
					);*/


	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 
			parent::__construct(); 
			
		//Setup rules that apply to all attributes
		$this->attributeRules = array(
			'default'	=> '1',
			'message'	=> 'Attributes must be small integers! We\'re not making a math game here',
			'required'	=> true,
			'rule'		=> 'numeric'
		);

		//Setup the validation
		$this->validate = array(
			'name' => array(
				'default'	=> 	'Default',
				'message' 	=> 	parent::$alphaNumericMessage,
				'required' 	=>	true,
				'rule'		=> 	'alphaNumeric'
			),
			'damage' 	=> $this->attributeRules,
			'defense' 	=> $this->attributeRules,
			'teamcost' 	=> $this->attributeRules,
			'playcost'  => $this->attributeRules
		);

	}
	
	//PUBLIC FUNCTION: getCardViewData
	//Get all the information necessary to display a card view
	public function getCardViewData( $uid ){
		
		return $this->find( 'first', array(
								'conditions' => array(
									'UnitType.uid'	=> $uid
								),
								
								'contain' => array(
									'UnitArtSet' => array(
									
									/*	'conditions' => array( 
											'UnitArtSet.name' => 'Default'
										),*/
										
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
										
										'UnitArtSetIcon' => array(
											'Icon' => array(
												
												'fields' => array(
													'Icon.image',
													'Icon.icon_positions_uid'
												)
												
											)
										)
										
									),
									'UnitTypeMovementSet' => array(
										'MovementSet' => array(
											
											'fields' => array(
												'MovementSet.name',
												'MovementSet.uid'
											),
											
											'Movement' => array(
											
												'fields' => array(
													'Movement.spaces',
													'Movement.priority'
												),
												
												'MovementDirectionSet' => array(
													'DirectionSet' => array(
														'DirectionSetDirection' => array(
															'Direction' => array(
															
																'fields' => array(
																	'Direction.x',
																	'Direction.y',
																	'Direction.name'
																)
																
															)
														)
													)
												)
											)
										)
									)
								),
								'fields' => array(
									'UnitType.name',
									'UnitType.damage',
									'UnitType.defense',
									'UnitType.teamcost'
								)
							));
		
	}
	
	//PUBLIC FUNCTION: getUIDs
	//Return a list of all the UIDs
	public function getUIDs(){
	
		return $this->find( 'list', array(
				'fields' =>  'UnitType.uid'		
			));	
		
	}
	
}

