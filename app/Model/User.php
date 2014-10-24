<?php

/**
 * Class User
 */
class User extends AppModel {

    //Establish the relations
    public $hasMany = [
        'UserEffectiveDate' => [
            'className'     => 'UserEffectiveDate',
            'foreignKey'    => 'users_uid'
        ]

    ];



    public $validate = [
        'username' => [
            'alphaNumeric' => [
                'rule'     => 'alphaNumeric',
                'required' => true,
                'message'  => 'Letters and numbers only'
            ],
            'between' => [
                'rule'    => ['between', 5, 15],
                'message' => 'Between 5 to 15 characters'
            ]
        ],
        'password' => [
            'alphaNumeric' => [
                'rule'     => 'alphaNumeric',
                'required' => true,
                'message'  => 'Letters and numbers only'
            ],
			'minimimum' => [
				'rule'    => ['minLength', '8'],
				'message' => 'Minimum 8 characters long'
			]
        ]
	];
	
	
	//PUBLIC FUNCTION: beforeSave
	//Handle anything that we need to do before saving a user to the database for the first time
	/**
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = []) {
        parent::beforeSave();
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password']);
		}
		return true;
	}
	
	//PUBLIC FUNCTION: getGamesByUserUID
	//Get the games that the user is involved in 
	/**
	 * @param $userUID
	 * @return array
	 */
	public function getGamesByUserUID( $userUID ){
		
		//Find all the games
		$allGames = $this->find( 'all', [
						'conditions' => [
							'users_uid' => $userUID
						]
					]);
					
		//Return the games 
		return $allGames;
		
	}
	
	//PUBLIC FUNCTION: setupNewUser
	//Function used to initialize all of the various data we'll need to have a proper
	//and usable user account. So let's do this.
	/**
	 * @param $username
	 * @param $password
	 */
	public function setupNewUser( $username, $password ){

        //Create a new user
        $this->create();
        $this->set( 'username', $username );
        $this->set( 'password', $password );
        $userUID = $this->save();

		//For now we'll settle with just setting up 30 random units for a new user
		//In order to do this we'll need an instance of a Unit model
		$unitModelInstance = ClassRegistry::init( 'Unit' );
		
		//And loop through it 30 times
		for( $freeUnitCounter = 0; $freeUnitCounter < 30; $freeUnitCounter++ ){
			$unitModelInstance->grantUserRandomUnit( $userUID );
		}
		
		//Setup a default team for the user
		$teamModelInstance = ClassRegistry::init( 'Team' );
		$teamModelInstance->setupDefaultTeam( $userUID );
		
	}
}

