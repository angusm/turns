<?php
class MovementSet extends AppModel {

	public $hasMany = array(
							'Movement' => array(
								'className'		=> 'Movement',
								'foreignKey'	=> 'movement_sets_uid'
							)
						);	
	
	public function __construct() { 
		parent::__construct(); 

		//Setup validation, let's not have any stupid names
		//for our direction sets.
		$this->validate = array(
			'name' => array(
				'rule'		=> 	'alphaNumeric',
				'required' 	=>	true,
				'message' 	=> 	parent::$alphaNumericMessage
			)
		);

	}
	
	//PUBLIC FUNCTION: findByUID
	//Find the given movement set by the UID
	public function findByUID( $uid ){
	
		//Find the UID
		return $this->find( 'first', array(
							'conditions' => array(
								'uid' => $uid
							),
							'contain' => array(
								'Movement' => array(
									'MovementDirectionSet' => array(
										'DirectionSet' => array(
											'DirectionSetDirection' => array(
												'Direction'
											)
										)
									)
								)
							)
						));
		
	}
	//PUBLIC FUNCTION: findByUIDWithPriority
	//Find the given movement set by the UID with the given priority
	public function findByUIDWithPriority( $uid, $priority ){
	
		//Find the UID
		return $this->find( 'first', array(
							'conditions' => array(
								'uid' => $uid
							),
							'contain' => array(
								'Movement' => array(
									'conditions' => array(
										'Movement.priority' => $priority
									),
									'MovementDirectionSet' => array(
										'DirectionSet' => array(
											'DirectionSetDirection' => array(
												'Direction'
											)
										)
									)
								)
							)
						));
		
	}
	
}

