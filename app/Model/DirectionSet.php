<?php
class DirectionSet extends AppModel {

	public $hasMany = array(
							'DirectionSetDirection' => array(
								'className'		=> 'DirectionSetDirection',
								'foreignKey'	=> 'direction_sets_uid'
							),
							'MovementDirectionSet' => array(
								'className'		=> 'MovementDirectionSet',
								'foreignKey'	=> 'direction_sets_uid'
							)
						);	
	
		public function __construct() { 
            parent::__construct(); 

			//Setup validation, let's not have any stupid names
			//for our direction sets.
			$this->validate = array(
				'name' => array(
                    'default'   =>  'Undefined',
					'rule'		=> 	'alphaNumeric',
					'required' 	=>	true,
					'message' 	=> 	parent::$alphaNumericMessage
				)
			);

		}
	
}

