<?php

/**
 * Class SiteLink
 */
class SiteLink extends AppModel{

    //Setup the site link's relations
    public $hasMany = [
        'MenuItem' => [
            'class'         => 'MenuItem',
            'foreign_key'   => 'site_links_uid'
        ],
        'SiteLinkContentRestriction' => [
            'class'         => 'SiteLinkContentRestriction',
            'foreign_key'   => 'site_links_uid'
        ]
    ];

    //CONSTRUCTOR
	/**
	 *
	 */
	public function __construct() {
        parent::__construct();

        //Setup validation, let's not have any stupid names
        //for our direction sets
        $this->validate = [
            'action' => [
                'default'   => '',
                'rule'		=> [ 'maxLength', 128 ],
                'required' 	=> true,
                'message' 	=> 'An action is the name of a function inside the controller'
            ],
            'controller' => [
                'default'   => '',
                'rule'      => [ 'maxLength', 128 ],
                'required'  => true,
                'message'   => 'A controller must be a defined class in the controllers folder'
            ]
        ];

    }

}