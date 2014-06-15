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
	
}

