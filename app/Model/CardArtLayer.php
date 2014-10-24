<?php

/**
 * Class CardArtLayer
 */
class CardArtLayer extends AppModel {

		//Override the constructor so that we can set the variables our way
		//and not some punk ass way we don't much like.
	/**
	 *
	 */
	public function __construct() {
	
			//Call the parent constructor
			parent::__construct();
			
			$this->validate = array_merge( 
						[
							'name' => [
								'rule'    => 'alphaNumeric',
								'required' 	=>	true,
								'message' 	=> 	parent::$alphaNumericMessage
							 ]
						],
						$this->validate
					);

		}
	
}

