<?php
class UnitStatMovementSet extends AppModel {

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

    //VALIDATION
    public $validate = [
        'movement_sets_uid' => [
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ],
        'unit_stats_uid' => [
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ]

    ];
	
	
}

