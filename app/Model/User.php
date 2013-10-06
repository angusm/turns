<?php
class User extends AppModel {
    public $validate = array(
        'username' => array(
            'alphaNumeric' => array(
                'rule'     => 'alphaNumeric',
                'required' => true,
                'message'  => 'Letters and numbers only'
            ),
            'between' => array(
                'rule'    => array('between', 5, 15),
                'message' => 'Between 5 to 15 characters'
            )
        ),
        'password' => array(
            'alphaNumeric' => array(
                'rule'     => 'alphaNumeric',
                'required' => true,
                'message'  => 'Letters and numbers only'
            ),
			'minimimum' => array(
				'rule'    => array('minLength', '8'),
				'message' => 'Minimum 8 characters long'
			)
        )
	);
	
	
	//PUBLIC FUNCTION: beforeSave
	//Handle anything that we need to do before saving a user to the database for the first time
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}
	
	//PUBLIC FUNCTION: setupNewUser
	//Function used to initialize all of the various data we'll need to have a proper
	//and usable user account. So let's do this.
	public function setupNewUser( $userUID ){
	
		//For now we'll settle with just setting up 30 random units for a new user
		//In order to do this we'll need an instance of a Unit model
		$unitModelInstance = ClassRegistry::init( 'Unit' );
		
		//And loop through it 30 times
		for( $freeUnitCounter = 0; $freeUnitCounter < 30; $freeUnitCounter++ ){
			$unitModelInstance->grantUserRandomUnit( $userUID );
		}
		
		
	}
}

