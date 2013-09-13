<?php
class IconPosition extends AppModel {

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 

		//Call the parent constructor
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

