<?php

/**
 * Class Movement
 */
class Movement extends AppModel {

	public $hasMany = [
							'MovementDirectionSet' => [
								'className'		=> 'MovementDirectionSet',
								'foreignKey'	=> 'movements_uid'
							]
						];
						
	public $belongsTo = [
							'MovementSet' => [
								'className'		=> 'MovementSet',
								'foreignKey'	=> 'movement_sets_uid'
							]
						];

	//PUBLIC FUNCTION: __construct
	//Override the constructor to setup some fun stuff
	/**
	 *
	 */
	public function __construct() {
	
		//Call the parent constructor
		parent::__construct();
		
		$this->validate = array_merge( 
					[
                        'movement_sets_uid' => [
                            'default'   => 1,
                            'rule'      => 'numeric',
                            'required'  => true
                        ],
                        'must_move_all_the_way' => [
                            'default'   => true,
                            'rule'      => 'boolean',
                            'required'  => true
                        ],
                        'priority' => [
                            'default'   => -1,
                            'rule'      => 'numeric',
                            'required' 	=>	true
                        ],
						'spaces' => [
                            'default'   => 1,
							'rule'      => 'numeric',
							'required' 	=>	true
						 ]
					],
					$this->validate
				);

	}
	
}

