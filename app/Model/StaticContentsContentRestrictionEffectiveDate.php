<?php

/**
 * Class StaticContentsContentRestrictionEffectiveDate
 */
class StaticContentsContentRestrictionEffectiveDate extends AppModel{

    //Setup the relation
    public $belongsTo = [
        'StaticContentsContentRestriction' => [
            'class'         => 'StaticContentsContentRestriction',
            'foreignKey'    => 'static_contents_content_restrictions_uid'
        ]
    ];

}