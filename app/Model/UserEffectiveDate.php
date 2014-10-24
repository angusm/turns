<?php

/**
 * Class UserEffectiveDate
 */
class UserEffectiveDate extends Appmodel{

    //Setup the relation
    public $belongsTo = [
        'User' => [
            'class'         => 'User',
            'foreignKey'    => 'users_uid'
        ]
    ];

}