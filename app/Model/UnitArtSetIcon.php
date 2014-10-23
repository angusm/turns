<?php
class UnitArtSetIcon extends AppModel {

	public $belongsTo = [
							'Icon' => [
								'className'		=> 'Icon',
								'foreignKey'	=> 'icons_uid'
							],
                            'UnitArtSet' => [
                                'className'     => 'UnitArtSet',
                                'foreignKey'    => 'unit_art_sets_uid'
                            ]
						];

    public $validate = [
        'icons_uid' => [
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ],
        'name' => [
            'default'   => 'Undefined',
            'required'  => true,
            'rule'      => [ 'maxLength', 64 ]
        ],
        'unit_art_sets_uid' => [
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ]
    ];

}

