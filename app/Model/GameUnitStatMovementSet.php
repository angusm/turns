<?php

/**
 * Class GameUnitStatMovementSet
 */
class GameUnitStatMovementSet extends AppModel {

	//Setup the relationship
	public $belongsTo = [
				'MovementSet' => [
					'className'		=> 'MovementSet',
					'foreignKey'	=> 'movement_sets_uid'
				],
				'GameUnitStat'	  => [
					'className'		=> 'GameUnitStat',
					'foreignKey'	=> 'game_unit_stats_uid'
				]
			];
			
	//PUBLIC FUNCTION: createRelationshipIfNoneExist
	//Create a relationship between movement sets and game unit stats if none
	//currently exist
	/**
	 * @param $unitStatMovementSetsArray
	 * @param $gameUnitStatUID
	 */
	public function createRelationshipIfNoneExist( $unitStatMovementSetsArray, $gameUnitStatUID ){
		
		//Loop through each unit stat movement set and see if an equivalent exists
		//in the game unit stat movement sets table, and if not just make it.
		foreach( $unitStatMovementSetsArray as $unitStatMovement ){
			
			//Check and see if such a relationship exists	
			$exists = $this->find( 'first', [
										'conditions' => [
											'GameUnitStatMovementSet.movement_sets_uid' => $unitStatMovement['movement_sets_uid'],
											'GameUnitStatMovementSet.game_unit_stats_uid' => $gameUnitStatUID
										]
									]);
			
			//If no such relationship exists then create it
			if( $exists == false ){
				
				$this->create();
				$this->set( 'movement_sets_uid', 	$unitStatMovement['movement_sets_uid'] );
				$this->set( 'game_unit_stats_uid', 	$gameUnitStatUID );
				$this->save();
				
			}
			
			//And move on to the next unit stat movement
			
		}
		
	}
	
	
}

