<?php
class CardArtLayer extends AppModel {

		//Override the constructor so that we can set the variables our way
		//and not some punk ass way we don't much like.
		public function __construct() { 
	
			//Call the parent constructor
			parent::__construct();
			
			$this->validate = array_merge( 
						array(
							'name' => array(
								'rule'    => 'alphaNumeric',
								'required' 	=>	true,
								'message' 	=> 	parent::$alphaNumericMessage
							 )
						),
						$this->validate
					);

		}
	
}

