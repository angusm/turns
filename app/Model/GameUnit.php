<?php
class GameUnit extends AppModel {

	//Setup the associations for this model
	public $belongsTo = array(
						'Game' => array(
							'className' 	=> 'Game',
							'foreignKey'	=> 'games_uid'
						),
						'TeamUnit' => array(
							'className' 	=> 'TeamUnit',
							'foreignKey'	=> 'team_units_uid'
						)
					);

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 

		//Call the parent constructor
		parent::__construct(); 
		
		$this->validate = array_merge(
					$this->validate
				);		

	}
	
}

