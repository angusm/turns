<?php
class Movement extends AppModel {

	public $hasMany = array(
							'MovementDirectionSet' => array(
								'className'		=> 'MovementDirectionSet',
								'foreignKey'	=> 'movements_uid'
							)
						);
						
	public $belongsTo = array( 
							'MovementSet' => array(
								'className'		=> 'MovementSet',
								'foreignKey'	=> 'movement_sets_uid'
							)
						);

	//PUBLIC FUNCTION: __construct
	//Override the constructor to setup some fun stuff
	public function __construct() { 
	
		//Call the parent constructor
		parent::__construct();
		
		$this->validate = array_merge( 
					array(
                        'movement_sets_uid' => array(
                            'default'   => 1,
                            'rule'      => 'numeric',
                            'required'  => true
                        ),
                        'must_move_all_the_way' => array(
                            'default'   => true,
                            'rule'      => 'boolean',
                            'required'  => true
                        ),
                        'priority' => array(
                            'default'   => -1,
                            'rule'      => 'numeric',
                            'required' 	=>	true
                        ),
						'spaces' => array(
                            'default'   => 1,
							'rule'      => 'numeric',
							'required' 	=>	true
						 )
					),
					$this->validate
				);

	}
	
}

