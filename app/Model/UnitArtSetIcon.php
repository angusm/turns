<?php
class UnitArtSetIcon extends AppModel {

	public $belongsTo = array(
							'Icon' => array(
								'className'		=> 'Icon',
								'foreignKey'	=> 'icons_uid'
							)
						);

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	public function __construct() { 

		//Call the parent constructor
		parent::__construct(); 

		//Call the parent function to setup the key validation for the relation			
		parent::setupUIDRelation( array( 'UnitArtSet', 'Icon' ) );

	}
	
}

