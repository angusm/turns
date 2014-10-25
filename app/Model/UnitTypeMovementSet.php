<?php

/**
 * Class UnitStatMovementSet
 */
class UnitTypeMovementSet extends AppModel {

		public $belongsTo = [
							'MovementSet' => [
								'className'		=> 'MovementSet',
								'foreignKey'	=> 'movement_sets_uid'
							],
							'UnitStat'	  => [
								'className'		=> 'UnitStat',
								'foreignKey'	=> 'unit_stats_uid'
							]
						];
	
}

