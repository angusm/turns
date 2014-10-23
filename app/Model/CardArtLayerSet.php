<?php
class CardArtLayerSet extends AppModel {

	public $belongsTo = [
							'CardArtLayer' => [
								'className'		=> 'CardArtLayer',
								'foreignKey'	=> 'unit_art_sets_uid'
							]
						];


	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 

		//Call the parent constructor
		parent::__construct();
			
		$this->validate = array_merge( 
					[
						'position' => [
							'rule'    	=> 'numeric',
							'required' 	=>	true,
							'message' 	=> 	parent::$numericMessage
						 ]
					],
					$this->validate
				);

	}
	
}

