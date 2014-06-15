<?php
class CardArtLayerMovement extends AppModel {

		//Override the constructor so that we can set the variables our way
		//and not some punk ass way we don't much like.
		public function __construct() { 
	
		//Call the parent constructor
		parent::__construct();
			
		$this->validate = array_merge( 
					array(
						'x_movement' => array(
							'rule'    	=> 'decimal',
							'message' 	=> 	parent::$decimalMessage
						 ),
						'y_movement' => array(
							'rule'    	=> 'decimal',
							'message' 	=> 	parent::$decimalMessage
						 )
					),
					$this->validate
				);

	}

}

