<?php

/**
 * Class MenuItem
 * @method Array children() children( Array )
 * @method recover()
 */
class MenuItem extends AppModel{

    //PROPERTIES

    /**
     * Since this model is essentially acting as a tree we use the tree behaviour
     * built into CakePHP to take care of a lot of nasty stuff for us. This will
     * allow us to take advantage of modified pre-ordered tree traversal without
     * having to think about modified pre-ordered tree traversal.
     * It does include a field for the parent in addition to the left and right fields
     * for the sake of making some functions and the management of the tree a lot
     * easier for us to work with.
     */
    public $actsAs = [
        'tree' => [
            'parent' => 'parent_uid'
        ]
    ];

    //Set the models relationships
    public $belongsTo = [
        'ParentMenuItem' => [
            'className'     => 'MenuItem',
            'foreignKey'    => 'parent_uid'
        ],
        'ParameterSet' => [
            'className'     => 'ParameterSet',
            'foreignKey'    => 'parameter_sets_uid'
        ],
        'SiteLink' => [
            'className'     => 'SiteLink',
            'foreignKey'    => 'site_links_uid'
        ]
    ];

    public $hasMany = [
	    'ChildMenuItem' => [
		    'className'     => 'MenuItem',
		    'foreignKey'    => 'parent_uid'
	    ],
        'MenuItemEffectiveDate' => [
            'className'     => 'MenuItemEffectiveDate',
            'foreignKey'    => 'menu_items_uid'
        ]
    ];

    //CONSTRUCTOR
	/**
	 *
	 */
	public function __construct() {
        parent::__construct();

        //Setup validation
        $this->validate = [
            'name' => [
                'default'   => 'New Menu Item',
                'rule'		=> [ 'maxLength', 64 ],
                'required' 	=> true,
                'message' 	=> 'A menu needs a name, 64 characters or less.'
            ],
            'site_links_uid' => [
                'default'   => '1',
                'rule'      => 'numeric',
                'required'  => true,
                'message'   => 'Must be a valid site link record UID'
            ]
        ];

    }


    //FUNCTIONS

	/**
	 * Return all of the menu items that are available to the given user
	 * @internal param null $userUID
	 * @return mixed
	 */
	public function getAvailableMenuItems(){

        //We start with the top level menu items and then recursively
        //grab each of the child menu items
        $availableMenuItems = $this->find(
	        'all',
	        [
		        'fields' => [
			        'lft',
			        'name'
		        ],
	            'contain' => [
		            'SiteLink' => [
			            'controller',
			            'action',
			            'name'
		            ],
		            'ParameterSet' => [
			            'Parameter' => [
				            'fields' => [
					            'key',
					            'value'
				            ]
			            ]
		            ]
				]
			]
        );

        return $availableMenuItems;

    }

    //PUBLIC FUNCTION: getChildMenuItems
    //Grab all of the menu items that belong to a specific parent, for the top
    //level menu items, their parentUID would be null
	/**
	 * @param null $parentUID
	 * @internal param null $userUID
	 * @return mixed
	 */
	public function getChildMenuItems( $parentUID=null ){

        //Grab all of the children's menu items
        $childMenuItems = $this->children( $parentUID );

        //Pass everything back to the controller
        return $childMenuItems;

    }



}
