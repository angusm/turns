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

    public $validate = array(
        'direction_sets_uid' => array(
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ),
        'movements_uid' => array(
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ),
        'name' => array(
            'default'   => 'Undefined',
            'rule'      => array( 'maxLength', 32 )
        )
    );
	
}

