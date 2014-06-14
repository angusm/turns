<?php

//Class contains all the information for the slideshow that sits at the top
//of each page in the layout
class HeaderSlide extends AppModel{


    //VARIABLES
    //Setup the relations to other models
    public $belongsTo   = array(
    );
    public $hasMany     = array(
        'HeaderSlideEffectiveDate'  => array(
            'class'         => 'HeaderSlideEffectiveDate',
            'foreignKey'    => 'header_slides_uid'
        )
    );

    //CONSTRUCTOR
    //Setup the validation, this is used by the TurnForm helper to
    //setup the various fields needed to manage data
    public function __construct(){
        parent::__construct();

        //Setup the validation
        $this->validate = array(
            'image' => array(
                'default'   => '',
                'rule'      => array('maxLength',128),
                'message'   => 'Image directory and filename must be under 128 characters.'
            ),
            'name'  => array(
                'default'   => '',
                'rule'      => array('maxLength', 128),
                'message'   => 'Slide names must be under 128 characters.'
            )
        );

    }

    //FUNCTIONS

    //PUBLIC FUNCTION: getAvailableSlideUIDs
    //Return the list of slides that ought to be viewable by the user provided any
    //applicable content restrictions
    public function getAvailableSlideUIDs(){

        $slideUIDs = $this->find( 'list', array(
            'fields' => array(
                'HeaderSlide.uid',
                'HeaderSlide.uid'
            )
        ));

        //Return the slide UIDs in a a sequentially indexed array
        return array_values( $slideUIDs );

    }

    //PUBLIC FUNCTION: getSlideData
    //Return the model information for a given slide
    public function getSlideData( $uid ){

        $slide = $this->find( 'first', array(
           'conditions' => array(
               'HeaderSlide.uid' => $uid
           )
        ));

        return $slide;

    }

}

?>