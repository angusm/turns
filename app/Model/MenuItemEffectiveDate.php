<?php

class MenuItemEffectiveDate extends AppModel{

    //Setup the relation
    public $belongsTo = array(
        'MenuItem' => array(
            'class'         => 'MenuItem',
            'foreignKey'    => 'menu_items_uid'
        )
    );

}