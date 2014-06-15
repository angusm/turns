<?php
class UnitStatMovementSet extends AppModel {

		public $belongsTo = array(
							'MovementSet' => array(
								'className'		=> 'MovementSet',
								'foreignKey'	=> 'movement_sets_uid'
							),
							'UnitStat'	  => array(
								'className'		=> 'UnitStat',
								'foreignKey'	=> 'unit_stats_uid'
							)
						);
	
}

