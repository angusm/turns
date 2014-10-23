<?php
class DirectionSet extends AppModel {

	public $hasMany = [
							'DirectionSetDirection' => [
								'className'		=> 'DirectionSetDirection',
								'foreignKey'	=> 'direction_sets_uid'
							],
							'MovementDirectionSet' => [
								'className'		=> 'MovementDirectionSet',
								'foreignKey'	=> 'direction_sets_uid'
							]
						];
	
		public function __construct() { 
            parent::__construct(); 

			//Setup validation, let's not have any stupid names
			//for our direction sets.
			$this->validate = [
				'name' => [
                    'default'   =>  'Undefined',
                    'message' 	=> 	parent::$alphaNumericMessage,
					'required' 	=>	true,
                    'rule'		=> 	parent::$alphaNumericWithSpacesValidationRule
				]
			];

		}
	
}

