<?php

class MenuItemEffectiveDate extends AppModel{

    //Setup the relation
    public $belongsTo = [
        'MenuItem' => [
            'class'         => 'MenuItem',
            'foreignKey'    => 'menu_items_uid'
        ]
    ];

}