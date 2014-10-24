<?php

/**
 * Class DirectionSetDirection
 */
class DirectionSetDirection extends AppModel {

	public $belongsTo = [
							'Direction' => [
								'className'		=> 'Direction',
								'foreignKey'	=> 'directions_uid'
							],
							'DirectionSet' => [
								'className'		=> 'DirectionSet',
								'foreignKey'	=> 'direction_sets_uid'
							]
						];
  
	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	/**
	 *
	 */
	public function __construct() {
		parent::__construct(); 



		//Now we're lucky that we're overriding the constructor
		//So we can get a list of potential values that are allowed
		//for this validation
		//Of course before we do that we need to get that list so here
		//we go
		
		//Setup the models, we'll need a list of direction set IDs as well
		//as a list of direction IDs
		$directionModel		= ClassRegistry::init('Direction');
		$directionSetModel 	= ClassRegistry::init('DirectionSet');
		
		//Now get the lists
		$directionUIDs		= $directionModel->getUIDList();
		$directionSetUIDs	= $directionSetModel->getUIDList();
		
		$this->validate = [
			 'directions_uid' => [
                 'default' => '1',
				 'rule'    => ['inList', $directionUIDs],
				 'message' => 'Must be a valid direction'
			 ],
			 'direction_sets_uid' => [
                 'default' => '1',
				 'rule'    => ['inList', $directionSetUIDs],
				 'message' => 'Must be a valid direction set'
			 ],
            'name' => [
                'default'   =>  'Undefined',
                'rule'		=> 	['maxLength',32],
                'required' 	=>	true,
                'message' 	=> 	parent::$alphaNumericMessage
            ]
		];

	}
	
}

