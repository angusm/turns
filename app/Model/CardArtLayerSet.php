<?php
class CardArtLayerSet extends AppModel {

	public $belongsTo = array(
							'CardArtLayer' => array(
								'className'		=> 'CardArtLayer',
								'foreignKey'	=> 'unit_art_sets_uid'
							)
						);


	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 

		//Call the parent constructor
		parent::__construct();
		
		//
		parent::setupUIDRelation( array( 'CardArtLayer', 'UnitArtSet' ) );
			
		$this->validate = array_merge( 
					array(
						'position' => array(
							'rule'    	=> 'numeric',
							'required' 	=>	true,
							'message' 	=> 	parent::$numericMessage
						 )
					),
					$this->validate
				);

	}
	
}

