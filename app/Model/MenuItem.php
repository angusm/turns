<?php

class MenuItem extends AppModel{

    //PROPERTIES

    //Since this model is essentially acting as a tree we use the tree behaviour
    //built into CakePHP to take care of a lot of nasty stuff for us. This will
    //allow us to take advantage of modified pre-ordered tree traversal without
    //having to think about modified pre-ordered tree traversal.
    //It does include a field for the parent in addition to the left and right fields
    //for the sake of making some functions and the management of the tree a lot
    //easier for us to work with.
    public $actsAs = array(
        'tree' => array(
            'parent_id' => 'parent_uid'
        )
    );

    //Set the models relationships
    public $belongsTo = array(
        'MenuItem' => array(
            'className'     => 'MenuItem',
            'foreignKey'    => 'parent_uid'
        ),
        'ParameterSet' => array(
            'className'     => 'ParameterSet',
            'foreignKey'    => 'parameter_sets_uid'
        ),
        'SiteLink' => array(
            'className'     => 'SiteLink',
            'foreignKey'    => 'site_links_uid'
        )
    );

    public $hasMany = array(
        'MenuItemEffectiveDate' => array(
            'className'     => 'MenuItemEffectiveDate',
            'foreignKey'    => 'menu_items_uid'
        )
    );

    //CONSTRUCTOR
    public function __construct() {
        parent::__construct();

        //Setup validation
        $this->validate = array(
            'name' => array(
                'default'   => 'New Menu Item',
                'rule'		=> array( 'maxLength', 64 ),
                'required' 	=> true,
                'message' 	=> 'A menu needs a name, 64 characters or less.'
            ),
            'site_links_uid' => array(
                'default'   => '1',
                'rule'      => 'numeric',
                'required'  => true,
                'message'   => 'Must be a valid site link record UID'
            )
        );

    }


    //FUNCTIONS

    //PUBLIC FUNCTION: getAvailableMenuItems
    //Return all of the menu items that are available to the given user
    public function getAvailableMenuItems( $userUID=null ){

        //We start with the top level menu items and then recursively
        //grab each of the child menu items
        $availableMenuItems = $this->children( array(
            'contain' => array(
                'SiteLink'
            )
        ));

        return $availableMenuItems;

    }

    //PUBLIC FUNCTION: getChildMenuItems
    //Grab all of the menu items that belong to a specific parent, for the top
    //level menu items, their parentUID would be null
    public function getChildMenuItems( $parentUID=null, $userUID=null ){

        //Grab all of the children's menu items
        $childMenuItems = $this->children( $parentUID );

        //Pass everything back to the controller
        return $childMenuItems;

    }



}
