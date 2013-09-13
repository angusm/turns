<?php
class MovementSet extends AppModel {

	public $hasMany = array(
							'Movement' => array(
								'className'		=> 'Movement',
								'foreignKey'	=> 'movement_sets_uid'
							)
						);	
	
		public function __construct() { 
            parent::__construct(); 

			//Setup validation, let's not have any stupid names
			//for our direction sets.
			$this->validate = array(
				'name' => array(
					'rule'		=> 	'alphaNumeric',
					'required' 	=>	true,
					'message' 	=> 	parent::$alphaNumericMessage
				)
			);

		}
	
}

