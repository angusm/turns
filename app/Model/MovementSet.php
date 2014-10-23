<?php
class MovementSet extends AppModel {

	public $hasMany = [
							'Movement' => [
								'className'		=> 'Movement',
								'foreignKey'	=> 'movement_sets_uid'
							],
                            'GameUnit' => [
                                'className'     => 'GameUnit',
                                'foreignKey'    => 'movement_sets_uid'
                            ]
						];
	
	public function __construct() { 
		parent::__construct(); 

		//Setup validation, let's not have any stupid names
		//for our direction sets.
		$this->validate = [
			'name' => [
                'default'   => 'default',
                'required' 	=>	true,
                'rule'		=> 	'alphaNumeric',
				'message' 	=> 	parent::$alphaNumericMessage
			]
		];

	}
	
	//PUBLIC FUNCTION: findByUID
	//Find the given movement set by the UID
	public function findByUID( $uid ){
	
		//Find the UID
		return $this->find( 'first', [
							'conditions' => [
								'uid' => $uid
							],
							'contain' => [
								'Movement' => [
									'MovementDirectionSet' => [
										'DirectionSet' => [
											'DirectionSetDirection' => [
												'Direction'
											]
										]
									]
								]
							]
						]);
		
	}
	//PUBLIC FUNCTION: findByUIDWithPriority
	//Find the given movement set by the UID with the given priority
	public function findByUIDWithPriority( $uid, $priority ){
	
		//Find the UID
		return $this->find( 'first', [
							'conditions' => [
								'uid' => $uid
							],
							'contain' => [
								'Movement' => [
									'conditions' => [
										'Movement.priority' => $priority
									],
									'MovementDirectionSet' => [
										'DirectionSet' => [
											'DirectionSetDirection' => [
												'Direction'
											]
										]
									]
								]
							]
						]);
		
	}
	
}

