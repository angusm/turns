<?php

/**
 * Class GameUnit
 */
class GameUnit extends AppModel {

	//Setup the associations for this model
	public $belongsTo = [
						'Game' => [
							'className'		=> 'Game',
							'foreignKey'	=> 'games_uid'
						],
						'GameUnitStat' => [
							'className'		=> 'GameUnitStat',
							'foreignKey'	=> 'game_unit_stats_uid'
						],
						'MovementSet' => [
							'className'		=> 'MovementSet',
							'foreignKey'	=> 'movement_sets_uid'
						],
						'UnitArtSet' => [
							'className'		=> 'UnitArtSet',
							'foreignKey'	=> 'unit_art_sets_uid'
						],
						'UnitType' => [
							'className'		=> 'UnitType',
							'foreignKey'	=> 'unit_types_uid'
						],
						'User' => [
							'className' 	=> 'User',
							'foreignKey'	=> 'users_uid'
						]
					];

	//Override the constructor so that we can set the variables our way
	//and not some punk ass way we don't much like.
	/**
	 *
	 */
	public function __construct() {

		//Call the parent constructor
		parent::__construct(); 
		
		$this->validate = array_merge(
					$this->validate
				);		

	}
	
	//PUBLIC FUNCTION: addToGameFromTeam
	//Take all the units in a given team and add them to a game
	/**
	 * @param $gameUID
	 * @param $teamUID
	 * @param bool $topPlayer
	 */
	public function addToGameFromTeam( $gameUID, $teamUID, $topPlayer=true ){
	
		//Setup whatever model instances we'll be needing
		$teamUnitModelInstance		= ClassRegistry::init( 'TeamUnit' );
		$gameUnitStatModelInstance 	= ClassRegistry::init( 'GameUnitStat' );
		
		//Grab the team's units
		$teamUnitTypes = $teamUnitModelInstance->getAllUnits( $teamUID );
		
		//Loop through all the unit types
		foreach( $teamUnitTypes as $teamUnitType ){
			//Loop through all of their positions
			foreach( $teamUnitType['TeamUnitPosition'] as $teamUnitPosition ){
			
				//If the player is the top player, then rotate their starting positions
				if( $topPlayer == true ){
					
					$teamUnitX = intval( $teamUnitPosition['x'] );
					$teamUnitY = 6 + intval( $teamUnitPosition['y'] );
					
				}else{
					
					$teamUnitX = 7 - intval( $teamUnitPosition['x'] );
					$teamUnitY = 1 - intval( $teamUnitPosition['y'] );
					
				}
			
				//Check if there's a game unit stat equivalent to the unit stat, if there is then we'll
				//use that for the new units, otherwise we'll create one in the rare event that this is
				//the first time this unit type with this unit stat has been used in a game.
				//May need to revisit this bullshit later when I'm older and wiser and less impatient and
				//have drunk fewer scotch ales
				
				$gameUnitStatUID = $gameUnitStatModelInstance->getUIDForUnitStat( $teamUnitType['UnitType']['UnitStat'] );
				
				//Setup the data
				$gameUnitData = [
									'GameUnit' => [
										'defense'					=> $teamUnitType['UnitType']['UnitStat']['defense'],
										'damage'					=> $teamUnitType['UnitType']['UnitStat']['damage'],
										'last_movement_angle'		=>  0,
										'last_movement_priority'	=>  0,
										'name'						=> $teamUnitType['UnitType']['name'],
										'turn'						=>  1,
										'x'							=> $teamUnitX,
										'y'							=> $teamUnitY,
										'games_uid' 				=> $gameUID,
										'game_unit_stats_uid'		=> $gameUnitStatUID,
										'unit_art_sets_uid'			=> $teamUnitType['UnitType']['unit_art_sets_uid'],
										'unit_types_uid'			=> $teamUnitType['UnitType']['uid'],
										'users_uid'					=> $teamUnitType['Team']['users_uid']
									]
								  ];
									
				//Create a new record for the unit and save it
				$this->create();
				$this->save( $gameUnitData );
				
			}
											
		}
			
	}
	
	//PUBLIC FUNCTION: areAllLastMovementPrioritiesZero
	//Check and see if all the last movement priorities for the active turn
	//of the given game are zero
	/**
	 * @param $gameUID
	 * @param $turn
	 * @return bool
	 */
	public function areAllLastMovementPrioritiesZero( $gameUID, $turn ){
	
		//Grab any unit that doesn't have a unit move priority of 0 for the
		//given game on the given turn. If such a unit can be grabbed then
		//return false, otherwise return true
		$lastMovedUnit = $this->find( 'first', [
									'conditions' => [
										'GameUnit.games_uid' 					=> $gameUID,
										'GameUnit.turn'							=> $turn,
										'GameUnit.last_movement_priority NOT' 	=> 0
									]
								]);
								
		if( $lastMovedUnit == false ){
			return true;
		}else{
			return false;
		}
		
	}
	
	//PUBLIC FUNCTION: findAllMovementSets
	//Find all the movement sets associated with the GameUnit
	/**
	 * @param $uid
	 * @return mixed
	 */
	public function findAllMovementSets( $uid ){
	
		//Grab all the movement sets 
		$gameUnit = $this->find( 'first', [
									'conditions' => [
										'uid' => $uid
									],
									'contain' => [
										'Unit' => [
											'UnitType' => [
												'UnitStat' => [
													'UnitStatMovementSet' => [
														'MovementSet'
													]
												]
											]
										]
									]
								]);
									
		//Return the movement sets
		return $gameUnit['Unit']['UnitType']['UnitStat']['UnitStatMovementSet'];
		
	}
	
	//PUBLIC FUNCTION: findAllMovementSetsWithPriority
	//Find all the movement sets associated with the GameUnit that have
	//the given priority
	/**
	 * @param $uid
	 * @param $priority
	 * @return mixed
	 */
	public function findAllMovementSetsWithPriority( $uid, $priority ){
	
		//Grab all the movement sets 
		$gameUnit = $this->find( 'first', [
									'conditions' => [
										'GameUnit.uid' => $uid
									],
									'contain' => [
										'UnitType' => [
											'UnitStat' => [
												'UnitStatMovementSet' => [
													'MovementSet' => [
														'Movement' => [
															'conditions' => [
																'priority' => $priority
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
												]
											]
										]
									]
								]);
									
		//Return the movement sets
		return $gameUnit['UnitType']['UnitStat']['UnitStatMovementSet'];
		
	}
	
	//PUBLIC FUNCTION: findForMoveValidation
	//Find the GameUnit with the given UID
	/**
	 * @param $uid
	 * @return array|bool
	 */
	public function findForMoveValidation( $uid ){
	
		//Return the GameUnit record
		$gameUnit =  $this->find( 'first', [
								'conditions' => [
									'GameUnit.uid' => $uid
								],
                                'fields' => [
                                    'GameUnit.uid',
                                    'GameUnit.game_identifier',
                                    'GameUnit.games_uid',
                                    'GameUnit.last_movement_angle',
                                    'GameUnit.last_movement_priority',
                                    'GameUnit.movement_sets_uid',
                                    'GameUnit.turn',
                                    'GameUnit.users_uid',
                                    'GameUnit.x',
                                    'GameUnit.y'
                                ],
								'contain' => [
									'Game' => [
                                        'fields' => [
                                            'Game.selected_unit_uid',
                                            'Game.turn'
                                        ],
										'UserGame'
									]
								]
							]);
	
		//Check to see if there's a unit in the same game
		//on the same turn that's already selected, if there is then
		//return false
		$otherUnit = $this->find( 'first', [
									'conditions' => [
                                        'GameUnit.uid NOT'          => $uid,
										'GameUnit.turn >'           => $gameUnit['GameUnit']['turn'],
										'GameUnit.games_uid'        => $gameUnit['GameUnit']['games_uid'],
                                        'GameUnit.game_identifier'  => $gameUnit['GameUnit']['game_identifier']
									]
								]);
		if( isset( $otherUnit['GameUnit.uid'] ) or
            $gameUnit['GameUnit']['turn'] > $gameUnit['Game']['turn'] ){
			return false;			
		}										
		
		return $gameUnit;
		
	}
	
	//PUBLIC FUNCTION: getInfoForCard
	//Return information about the game unit so that a card display can be created from it 
	//with javascript
	/**
	 * @param $uid
	 * @return array
	 */
	public function getInfoForCard( $uid ){
		
		$cardInfo = $this->find( 'first', [
									'conditions' => [
										'GameUnit.uid' => $uid
									],
									'contain'	=> [
										'GameUnitStat' => [
											'GameUnitStatMovementSet' => [
												'MovementSet' => [
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
											]
										],
										'UnitArtSet' => [
											'UnitArtSetIcon' => [
												'Icon'
											],
											'CardArtLayerSet' => [
												'CardArtLayer'
											]
										]
									]
								]);
								
		return $cardInfo;
		
	}
	
	//PRIVATE FUNCTION: moveGameUnitToNextTurn
	//Move the game unit to the next turn
	/**
	 * @param $unitToMove
	 * @param $gameTurn
	 */
	private function moveGameUnitToNextTurn( $unitToMove, $gameTurn ){
	
		//Update the turn
		$unitToMove['GameUnit']['turn'] = $gameTurn + 1;
						
		//Move the unit up
		$unitToMoveGameUnit = $unitToMove['GameUnit'];
		$unitToMove = [
						'GameUnit' => $unitToMoveGameUnit
					];
			
		$this->read( NULL, $unitToMove['GameUnit']['uid'] );
		$this->save( $unitToMove );
			
	}
	
	//PUBLIC FUNCTION: moveGameUnitsToNextTurn
	//Take all the units tied up in the current game and move them on to the next turn
	/**
	 * @param $gameUID
	 * @param $turn
	 * @param $gameUnitUID
	 * @param $targetX
	 * @param $targetY
	 * @param $angle
	 * @param $movePriority
	 * @param $movementSetUID
	 */
	public function moveGameUnitsToNextTurn(
										$gameUID, 
										$turn, 
										$gameUnitUID, 
										$targetX, 
										$targetY, 
										$angle, 
										$movePriority, 
										$movementSetUID ){

		//Grab all of the relevant units on the current turn and then move 
		//them forward a turn, with the exception of the moved unit. 
		
		//Grab the relevant units
		$unitsToMove = $this->find( 'all', [
							'conditions' => [
                                'GameUnit.defense >'    => 0,
                                'GameUnit.games_uid'	=> $gameUID,
                                'GameUnit.turn <=' 		=> $turn,
								'GameUnit.uid NOT'		=> $gameUnitUID
							],
							'contain' => [
								'GameUnitStat' => [
                                    'fields' => [
                                        'GameUnitStat.uid',
                                        'GameUnitStat.damage'
                                    ]
                                ]
							],
                            'group' => [
                                'GameUnit.game_identifier'
                            ],
                            'order' => [
                                'GameUnit.turn DESC'
                            ]
						]);

		$movedUnit 	= $this->find( 'first', [
							'conditions' => [
								'GameUnit.games_uid'	=> $gameUID,
								'GameUnit.turn <=' 		=> $turn,
								'GameUnit.uid'			=> $gameUnitUID
							],
							'contain' => [
                                'GameUnitStat' => [
                                    'fields' => [
                                        'GameUnitStat.uid',
                                        'GameUnitStat.damage'
                                    ]
                                ],
                                'Game' => [
                                    'fields' => [
                                        'Game.turn'
                                    ]
                                ]
							]
                        ]);
			
		//Store the original unit so we can record a record of it.
		$this->storeOriginalUnit( $movedUnit );
			
		//Update the values
		$movedUnit['GameUnit']['x'] 						= $targetX;
		$movedUnit['GameUnit']['y'] 						= $targetY;				
		$movedUnit['GameUnit']['last_movement_angle'] 		= $angle;
		$movedUnit['GameUnit']['last_movement_priority'] 	= $movePriority + 1;
		$movedUnit['GameUnit']['movement_sets_uid'] 		= $movementSetUID;
			
		//Now we need to check to make sure that the last active unit still
		//has moves left before it is finished moving. If it doesn't then we
		//set it's last_movement_priority back to 0.
		//To do this we need to find a movement tied to the given movement set
		//with the new priority
		$movementModelInstance = ClassRegistry::init( 'Movement' );
		$validNextMove = $movementModelInstance->find( 'first', [
                                                            'fields' => [
                                                                'Movement.uid'
                                                            ],
															'conditions' => [
																'movement_sets_uid' => $movementSetUID,
																'priority'			=> $movedUnit['GameUnit']['last_movement_priority']
															]
														]);

		//If the unit has a valid next move set it as the selected unit, otherwise clear its
		//movement stats
		if( $validNextMove == false ){
			$movedUnit['GameUnit']['last_movement_priority'] 	= 0;
			$movedUnit['GameUnit']['last_movement_angle'] 		= 0;
			$movedUnit['GameUnit']['movement_sets_uid'] 		= NULL;
			$this->selectUnit( NULL, $gameUID );
            $preserveDamage = false;
		}else{
			$this->selectUnit( $gameUnitUID, $gameUID );
            $preserveDamage = true;
		}

		//Loop through the found units and make a list of any that need to be updated
        //We use this to check if the damage should be preserved
		foreach( $unitsToMove as $unitToMoveIndex => $unitToMove ){

			//We need to check if the current unit was positioned where the moved
			//unit landed, if this is the case then we have some serious work cut
			//out for us
			if( $unitToMove['GameUnit']['x'] == $targetX and
                $unitToMove['GameUnit']['y'] == $targetY and
                $validNextMove == false ){

				//Well looks like the moved unit bumped another unit, now we need
				//to check if the bumped unit was friendly or an enemy
				if( $unitToMove['GameUnit']['users_uid'] == $movedUnit['GameUnit']['users_uid'] ){

					//If the unit is friendly then buff the damage of the bumped unit
                    $this->storeOriginalUnit( $unitToMove );
					$unitToMove['GameUnit']['damage'] += $movedUnit['GameUnit']['damage'];
					$this->selectUnit( $unitToMove['GameUnit']['uid'], $gameUID );
                    $this->moveGameUnitToNextTurn( $unitToMove, $movedUnit['Game']['turn'] );
                    $preserveDamage = true;

				}else{

					//If the unit is an enemy check if the moved unit has enough damage
					//to destroy it or if the moved unit should be destroyed.
					if( $unitToMove['GameUnit']['defense'] > $movedUnit['GameUnit']['damage'] ){
						$movedUnit['GameUnit']['defense']   = 0;
					}else{
                        $this->storeOriginalUnit( $unitToMove );
						$unitToMove['GameUnit']['defense']  = 0;
                        $this->moveGameUnitToNextTurn( $unitToMove, $movedUnit['Game']['turn'] );
					}
					$this->selectUnit( NULL, $gameUID );

				}

                $unitsToMove[$unitToMoveIndex] = $unitToMove;

            //Unset any unaffected units that were already dead
			}else{

                //If the unit was already dead, drop it
                if( $unitToMove['GameUnit']['defense'] < 1 ){
                    unset( $unitsToMove[$unitToMoveIndex] );
                }

            }

		}
		//If we're not going to be preserving the damage then we must move any units with a higher
        //damage then their default to the next turn with restored default damage
		if( ! $preserveDamage ){

            //Reset the moved unit's damage
			$movedUnit['GameUnit']['damage'] = $movedUnit['GameUnitStat']['damage'];

            //Loop through and reset unmoved units damage as necessary, other units are removed
			foreach( $unitsToMove as $unitToMoveIndex => $unitToMove ){
                if( $unitToMove['GameUnit']['damage']   != $unitToMove['GameUnitStat']['damage'] and
                    $unitToMove['GameUnit']['defense']  != 0 ){

                    //Store the original unit and then update the unit
                    $this->storeOriginalUnit( $unitToMove );
				    $unitToMove['GameUnit']['damage'] = $unitToMove['GameUnitStat']['damage'];
                    $this->moveGameUnitToNextTurn( $unitToMove, $movedUnit['Game']['turn'] );

                }else{
                    unset( $unitsToMove[$unitToMoveIndex] );
                }
			}
		}
		
		//Move the unit that moved
		$this->moveGameUnitToNextTurn( $movedUnit, $movedUnit['Game']['turn'] );
				
		
	}
	
	//PUBLIC FUNCTION: playerHasActiveUnit
	//Return a true false result indicating whether or not the player
	//still has active living units in the game
	/**
	 * @param $userUID
	 * @param $gameUID
	 * @param $turn
	 * @return bool
	 */
	public function playerHasActiveUnit( $userUID, $gameUID, $turn ){
		
		$exists = $this->find( 'first', [
									'conditions' => [
										'GameUnit.users_uid'	=> $userUID,
										'GameUnit.games_uid' 	=> $gameUID,
										'GameUnit.defense >'	=> 0,
										'GameUnit.turn'			=> $turn
									]
								]);
								
		if( $exists != false ){
			return true;
		}else{
			return false;
		}
		
	}

    //PUBLIC FUNCTION: selectUsingArray
    //Grab a collection of units given a passed array
	/**
	 * @param $givenArray
	 * @return array
	 */
	public function selectUsingArray( $givenArray ){

        //Grab the units
        return $this->find( 'all', $givenArray );

    }
	
	//PUBLIC FUNCTION: selectUnit
	//Set the game unit to as the selected unit for the given game
	/**
	 * @param $gameUnitUID
	 * @param $gameUID
	 */
	public function selectUnit( $gameUnitUID, $gameUID ){
		
		//Setup the game model instance
		$gameModelInstance = ClassRegistry::init( 'Game' );
		
		//Change the selected unit UID		
		$gameModelInstance->read( NULL, $gameUID );
		$gameModelInstance->set( 'selected_unit_uid', $gameUnitUID );
		$gameModelInstance->save();
		
	}
	
	//PUBLIC FUNCTION: storeOriginalUnit
	//Store the original unit
	/**
	 * @param $newUnit
	 * @return array|mixed
	 */
	public function storeOriginalUnit( $newUnit ){
		
			$originalGameUnit = $newUnit['GameUnit'];
			unset( $originalGameUnit['uid'] );
			$originalGameUnit = [
									'GameUnit' => $originalGameUnit
								];
								
			//Create a new record and save the original game unit
			//This is how we'll preserve the integrity of every turn
			$this->create();
			$originalGameUnit = $this->save( $originalGameUnit );
			
			//Return the original game unit
			return $originalGameUnit;
		
	}
	
}

