<?php

//Class contains all the information for the slideshow that sits at the top
//of each page in the layout
/**
 * Class HeaderSlide
 */
class HeaderSlide extends AppModel{


    //VARIABLES
    //Setup the relations to other models
    public $belongsTo   = [
    ];
    public $hasMany     = [
        'HeaderSlideEffectiveDate'  => [
            'class'         => 'HeaderSlideEffectiveDate',
            'foreignKey'    => 'header_slides_uid'
        ]
    ];

    //CONSTRUCTOR
    //Setup the validation, this is used by the TurnForm helper to
    //setup the various fields needed to manage data
	/**
	 *
	 */
	public function __construct(){
        parent::__construct();

        //Setup the validation
        $this->validate = [
            'image' => [
                'default'   => '',
                'rule'      => ['maxLength',128],
                'message'   => 'Image directory and filename must be under 128 characters.'
            ],
            'name'  => [
                'default'   => '',
                'rule'      => ['maxLength', 128],
                'message'   => 'Slide names must be under 128 characters.'
            ]
        ];

    }

    //FUNCTIONS

    //PUBLIC FUNCTION: getAvailableSlideUIDs
    //Return the list of slides that ought to be viewable by the user provided any
    //applicable content restrictions
	/**
	 * @return array
	 */
	public function getAvailableSlideUIDs(){

        $slideUIDs = $this->find( 'list', [
            'fields' => [
                'HeaderSlide.uid',
                'HeaderSlide.uid'
            ]
        ]);

        //Return the slide UIDs in a a sequentially indexed array
        return array_values( $slideUIDs );

    }

    //PUBLIC FUNCTION: getSlideData
    //Return the model information for a given slide
	/**
	 * @param $uid
	 * @return array
	 */
	public function getSlideData( $uid ){

        $slide = $this->find( 'first', [
           'conditions' => [
               'HeaderSlide.uid' => $uid
           ]
        ]);

        return $slide;

    }

}