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
	
}

