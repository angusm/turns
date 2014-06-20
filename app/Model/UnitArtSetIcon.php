<?php
class UnitArtSetIcon extends AppModel {

	public $belongsTo = array(
							'Icon' => array(
								'className'		=> 'Icon',
								'foreignKey'	=> 'icons_uid'
							),
                            'UnitArtSet' => array(
                                'className'     => 'UnitArtSet',
                                'foreignKey'    => 'unit_art_sets_uid'
                            )
						);

    public $validate = array(
        'icons_uid' => array(
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ),
        'name' => array(
            'default'   => 'Undefined',
            'required'  => true,
            'rule'      => array( 'maxLength', 64 )
        ),
        'unit_art_sets_uid' => array(
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        )
    );

}

