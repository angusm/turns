<?php

class StaticContent extends AppModel{

    //Setup the relations
    public $hasMany = array(
        'StaticContentEffectiveDate' => array(
            'class'         => 'StaticContentEffectiveDate',
            'foreignKey'    => 'static_contents_uid'
        ),
        'StaticContentsContentRestriction' => array(
            'class'         => 'StaticContentsContentRestriction',
            'foreignKey'    => 'static_contents_uid'
        )
    );

    //VALIDATION
    public $validate = array(
        'name' => array(
            'default'   => 'Content',
            'rule'      => array('maxLength',64)
        )
    );

    //FUNCTIONS

    //PUBLIC FUNCTION: beforeFind
    //Extending the behaviour of the AppModel beforeFind to take content
    //restrictions into account
    public function beforeFind( $query ){

        //Respect your elders
        $query = parent::beforeFind($query);

        //Initialize a UserClinic model so that we can use the UID of the
        //currently authenticated user to find all of the relevant content
        //restrictions that apply to them
        $user = $this->getAuthUser();

        return $query;


    }


}

?>