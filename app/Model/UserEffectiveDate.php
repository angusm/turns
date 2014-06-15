<?php

class UserEffectiveDate extends Appmodel{

    //Setup the relation
    public $belongsTo = array(
        'User' => array(
            'class'         => 'User',
            'foreignKey'    => 'users_uid'
        )
    );

}