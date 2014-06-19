<?php

class StaticContentsContentRestriction extends AppModel{

    //Setup the relation
    public $belongsTo = array(
        'StaticContent' => array(
            'class'         => 'StaticContent',
            'foreignKey'    => 'static_contents_uid'
        ),
        'ContentRestriction' => array(
            'class'         => 'ContentRestriction',
            'foreignKey'    => 'content_restrictions_uid'
        )
    );
    public $hasMany = array(
        'StaticContentsContentRestrictionEffectiveDate' => array(
            'class'         => 'StaticContentsContentRestrictionEffectiveDate',
            'foreignKey'    => 'static_contents_content_restrictions_uid'
        )
    );

}

?>