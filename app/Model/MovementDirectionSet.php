<?php

/**
 * Class MovementDirectionSet
 */
class MovementDirectionSet extends AppModel {

	public $belongsTo = [
        'DirectionSet' 	=> [
            'className'		=> 'DirectionSet',
            'foreignKey'	=> 'direction_sets_uid'
        ],
        'Movement'		=> [
            'className'		=> 'Movement',
            'foreignKey'	=> 'movements_uid'
        ]
    ];

    public $validate = [
        'direction_sets_uid' => [
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ],
        'movements_uid' => [
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ],
        'name' => [
            'default'   => 'Undefined',
            'rule'      => [ 'maxLength', 32 ]
        ]
    ];
	
}

