<?php

//Setup the parameter class, this class will contain key value pairs to attach to
//requests to controller actions
class Parameter extends AppModel{

    //Setup the belongs to model associations
    public $belongsTo = array(
        'ParameterSet' => array(
            'class'         => 'ParameterSet',
            'foreignKey'    => 'parameter_sets_uid'
        )
    );

}

?>