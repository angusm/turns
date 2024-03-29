<?php

//Setup the parameter set model. This model will contain sets of key value
//pairs through its association with Parameter that can then be passed as a group
//to a controller's action during a request
/**
 * Class ParameterSet
 */
class ParameterSet extends AppModel{

    //Setup its relations with other models
    public $hasMany = [
        'Parameter' => [
            'class'         => 'Parameter',
            'foreign_key'   => 'parameter_sets_uid'
        ],
        'MenuItem' => [
            'class'         => 'MenuItem',
            'foreign_key'   => 'parameter_sets_uid'
        ]

    ];
}