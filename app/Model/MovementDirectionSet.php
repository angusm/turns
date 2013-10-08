<?php
class MovementDirectionSet extends AppModel {

	public $belongsTo = array(
							'DirectionSet' 	=> array(
								'className'		=> 'DirectionSet',
								'foreignKey'	=> 'direction_sets_uid'
							),
							'Movement'		=> array(
								'className'		=> 'Movement',
								'foreignKey'	=> 'movements_uid'
							)
						);	
						
		public function __construct() { 
            parent::__construct(); 
			
			//Now we're lucky that we're overriding the constructor
			//So we can get a list of potential values that are allowed
			//for this validation
			//Of course before we do that we need to get that list so here
			//we go

			//Call the parent function to setup the key validation for the
			//relation			
			parent::setupUIDRelation( array( 'Movement', 'DirectionSet' ) );

		}
	
}

