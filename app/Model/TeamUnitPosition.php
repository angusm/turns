<?php
class TeamUnitPosition extends AppModel {
	
	//Setup the associations for UnitType
	public $belongsTo = array(
						'TeamUnit'	=> array(
							'className'		=> 'TeamUnit',
							'foreignKey'	=> 'team_units_uid'
						)						
					);

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 
			parent::__construct(); 
			
		//Setup the validation
		$this->validate = array(
			'x' => array(
				'default'	=> 	'-1',
				'message' 	=> 	parent::$numericMessage,
				'required' 	=>	true,
				'rule'		=> 	'numeric'
			),
			'y' => array(
				'default'	=> 	'-1',
				'message' 	=> 	parent::$numericMessage,
				'required' 	=>	true,
				'rule'		=> 	'numeric'
			)
		);

	}
	
	//PUBLIC FUNCTION: assignPosition
	//Assign a position to the given team units
	public function assignPosition( $teamUID, $x, $y ){
		
		//Make sure we don't already have a unit in that position
		//and that the unit is in the allowed range, we're going with
		//the starting positions of chess, so from 0,0 to 7,1
		if( $x > 7 or $y > 1 ){
			return false;
		}
		
		//Now we check that we don't already have such a unit
		$exists = $this->find( 'first', array(
		
	}
	
	//PUBLIC FUNCTION: getUIDs
	//Return a list of all the UIDs
	public function getUIDs(){
	
		return $this->find( 'list', array(
				'fields' =>  'UnitType.uid'		
			));	
		
	}
	
}

