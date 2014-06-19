<?php

class StaticContentEffectiveDate extends  AppModel{

    public $belongsTo = array(
        'StaticContent' => array(
            'class'         => 'StaticContent',
            'foreignKey'    => 'static_contents_uid'
        )
    );

}

?>