<?php

/**
 * Class GameUnitsController
 */
class GameUnitsController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
    }
	
	//PUBLIC FUNCTION: getGameUnitCardInfo
	//Return JSON data to a client when they need to know about a 
	//possible new state of the game
	public function getGameUnitCardInfo(){
		
		//Grab the JSON Data
		$jsonData 		= $this->params['url'];
		
		//Grab the game unit uid
		$gameUnitUID	= $jsonData['uid'];
		
		//Grab the game unit's info
		$unitInfo = $this->GameUnit->getInfoForCard( $gameUnitUID );
		
		//And everything else will be handled by the View and Javascript
		//Scary huh?
		$this->set( 'unitInfo', 	$unitInfo );
		$this->set( '_serialize', [
						'unitInfo'
						]
					);
		
	}
	
}