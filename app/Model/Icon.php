<?php
class Icon extends AppModel {
	
	//Setup associations
	public $belongsTo = array(
							'IconPosition' => array(
								'className'		=> 'IconPosition',
								'foreignKey'	=> 'icon_positions_uid'
							)
						);

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 

		//Call the parent constructor
		parent::__construct();
		
		$this->validate = array_merge( 
					array(
						'image' => array(
							'required' 	=>	true
						 )
					),
					$this->validate
				);
		
		

	}
	
}

