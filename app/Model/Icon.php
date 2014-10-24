<?php

/**
 * Class Icon
 */
class Icon extends AppModel {
	
	//Setup associations
	public $belongsTo = [
							'IconPosition' => [
								'className'		=> 'IconPosition',
								'foreignKey'	=> 'icon_positions_uid'
							]
						];

    //VALIDATION
    public $validate = [
        'icon_positions_uid' => [
            'default'   => 3,
            'required'  => true,
            'rule'      => 'numeric'
        ],
        'image' => [
            'default'   => 'CardArt/Default/boardIcon.png',
            'required' 	=> true,
            'rule'      => [ 'maxLength', 64 ]
        ],
        'name' => [
            'default'   => 'Undefined',
            'required'  => true,
            'rule'      => [ 'maxLength', 64 ]
        ]
    ];
	
}

