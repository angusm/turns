<?php

/**
 * Class GameUnitStat
 */
class GameUnitStat extends AppModel {
	
	//Setup the associations for GameUnitStat
	public $hasMany = [
						'GameUnit'	=> [
							'className'		=> 'GameUnit',
							'foreignKey'	=> 'game_unit_stats_uid'
						],
						'GameUnitStatMovementSet'	=> [
							'className'		=> 'GameUnitStatMovementSet',
							'foreignKey'	=> 'game_unit_stats_uid'
						]
							
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
	
	//PUBLIC FUNCTION: getUIDForUnitStat
	//Take in a data array for a unit stat and see if we have a game unit stat
	//that matches it in every way. If we do return that UID, if we don't, make it
	//and then return that UID
	/**
	 * @param $unitStatArray
	 * @return mixed
	 */
	public function getUIDForUnitStat( $unitStatArray ){
	
		//Find a game unit stat that matches the given unit stat
		$exists = $this->find( 'first', [
									'conditions' => [
										'name' 		=> $unitStatArray['name'],
										'damage' 	=> $unitStatArray['damage'],
										'defense' 	=> $unitStatArray['defense'],
										'teamcost' 	=> $unitStatArray['teamcost'],
										'playcost'	=> $unitStatArray['playcost']
									]
								]);
	
		//If no such game unit stat exists, then create it.
		if( $exists == false ){
			$this->create();
			$this->set('name', 		$unitStatArray['name'] );
			$this->set('damage', 	$unitStatArray['damage'] );
			$this->set('defense', 	$unitStatArray['defense'] );
			$this->set('teamcost', 	$unitStatArray['teamcost'] );
			$this->set('playcost', 	$unitStatArray['playcost'] );
			$this->save();
			
			$created = $this->getUIDForUnitStat( $unitStatArray );
			
			return $created;
			
		//If we've got a game unit stat that fits the requirements then we just have to
		//make sure we have a game unit stat movement set that matches it
		}else{
			
			//Setup a game unit stat movement set instance
			$gameUnitStatMovementSetModelInstance = ClassRegistry::init( 'GameUnitStatMovementSet' );
			$gameUnitStatMovementSetModelInstance->createRelationshipIfNoneExist( $unitStatArray['UnitStatMovementSet'], $exists['GameUnitStat']['uid'] ); 
						
			return $exists['GameUnitStat']['uid'];
		
		}
											
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
	
}

