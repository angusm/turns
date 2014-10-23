<?php
class UnitStat extends AppModel {
	
	//Setup the associations for UnitType
	public $hasMany = [
						'UnitType'	=> [
							'className'	=> 'UnitType',
							'foreignKey'	=> 'unit_stats_uid'
						],
						'UnitStatMovementSet' => [
							'className'		=> 'UnitStatMovementSet',
							'foreignKey'	=> 'unit_stats_uid'
						]
					];

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 
			parent::__construct(); 
			
		//Setup rules that apply to all attributes
		$this->attributeRules = [
			'default'	=> '1',
			'message'	=> 'Attributes must be small integers! We\'re not making a math game here',
			'required'	=> true,
			'rule'		=> 'numeric'
		];

		//Setup the validation
		$this->validate = [
			'name' => [
				'default'	=> 	'Default',
				'message' 	=> 	parent::$alphaNumericMessage,
				'required' 	=>	true,
				'rule'		=> 	'alphaNumeric'
			],
			'damage' 	=> $this->attributeRules,
			'defense' 	=> $this->attributeRules,
			'teamcost' 	=> $this->attributeRules,
			'playcost'  => $this->attributeRules
		];

	}
	
	//PUBLIC FUNCTION: getUIDs
	//Return a list of all the UIDs
	public function getUIDs(){
	
		return $this->find( 'list', [
				'fields' =>  'UnitType.uid'		
			]);
		
	}
	
}

