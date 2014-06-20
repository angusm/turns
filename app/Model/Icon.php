<?php
class Icon extends AppModel {
	
	//Setup associations
	public $belongsTo = array(
							'IconPosition' => array(
								'className'		=> 'IconPosition',
								'foreignKey'	=> 'icon_positions_uid'
							)
						);

    //VALIDATION
    public $validate = array(
        'icon_positions_uid' => array(
            'default'   => 3,
            'required'  => true,
            'rule'      => 'numeric'
        ),
        'image' => array(
            'default'   => 'CardArt/Default/boardIcon.png',
            'required' 	=> true,
            'rule'      => array( 'maxLength', 64 )
        ),
        'name' => array(
            'default'   => 'Undefined',
            'required'  => true,
            'rule'      => array( 'maxLength', 64 )
        )
    );
	
}

