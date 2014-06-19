<?php

class StaticContentsContentRestrictionEffectiveDate extends AppModel{

    //Setup the relation
    public $belongsTo = array(
        'StaticContentsContentRestriction' => array(
            'class'         => 'StaticContentsContentRestriction',
            'foreignKey'    => 'static_contents_content_restrictions_uid'
        )
    );

}

?>