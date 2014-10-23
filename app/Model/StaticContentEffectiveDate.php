<?php

class StaticContentEffectiveDate extends  AppModel{

    public $belongsTo = [
        'StaticContent' => [
            'class'         => 'StaticContent',
            'foreignKey'    => 'static_contents_uid'
        ]
    ];

}