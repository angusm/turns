<?php

/**
 * Class StaticContentsContentRestriction
 */
class StaticContentsContentRestriction extends AppModel{

    //Setup the relation
    public $belongsTo = [
        'StaticContent' => [
            'class'         => 'StaticContent',
            'foreignKey'    => 'static_contents_uid'
        ],
        'ContentRestriction' => [
            'class'         => 'ContentRestriction',
            'foreignKey'    => 'content_restrictions_uid'
        ]
    ];
    public $hasMany = [
        'StaticContentsContentRestrictionEffectiveDate' => [
            'class'         => 'StaticContentsContentRestrictionEffectiveDate',
            'foreignKey'    => 'static_contents_content_restrictions_uid'
        ]
    ];

}