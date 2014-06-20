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

    //VALIDATION
    public $validate = array(
        'movement_sets_uid' => array(
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        ),
        'unit_stats_uid' => array(
            'default'   => 1,
            'required'  => true,
            'rule'      => 'numeric'
        )

    );
	
	
}

