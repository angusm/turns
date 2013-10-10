<?php

//This class will be used to make badass forms just the way I like them

App::uses('AppHelper', 'View/Helper');

class GamePlayHelper extends AppHelper {
	
	//We'll be using some of the HTML helper's functionality to do awesome stuff
  	var $helpers = array('Html');
	
	//PUBLIC FUNCTION: gameplayJS
	//Add the necessary base JS
	public function gameplayJS(){
		
		// JavaScript Document
		
		//Create the array for all the units
		return 'window.playerUnits 	= new Array();'.
		'window.enemyUnits 	= new Array();'.
		
		//Alright let's do this matchmaking stuff
		'function unit( nuX, nuY, nuName, nuDefense, nuDamage, nuUid, nuMovements ){'.
			
			'this.x			= nuX;'.
			'this.y			= nuY;'.
			'this.name 		= nuName;'.
			'this.defense	= nuDefense;'.
			'this.damage 	= nuDamage;'.
			'this.uid 		= nuUid;'.
			'this.movements	= nuMovements;'.
			
		'}'.
		
		'function movement( nuMustMoveAllTheWay, nuSpaces, nuDirections ){'.
		
			'this.directions			= nuDirections;'.
			'this.mustMoveAllTheWay 	= nuMustMoveAllTheWay;'.
			'this.spaces				= nuSpaces;'.
			
		'}';
	}
	
	//PUBLIC FUNCTION: renderBoard
	//Render the game board, it'll be beautiful
	public function renderBoard( $board ){
		
		//Setup a string that we'll pack full of garbage that we can return later
		$tilesString = '';
		
		//Do a double loop through height and width to render every damn tile of 
		//this board
		for( $xCounter = 0; $xCounter < $board['width']; $xCounter++ ){
			for( $yCounter = 0; $yCounter < $board['height']; $yCounter++ ){
				
				//Add the tile to the return string
				$tilesString .= $this->renderTile(
											$xCounter,
											$yCounter
										);
				
			}	
		}
		
		//Slam all these tiles into a board DIV and call it a day
		return $this->Html->tag(
								'div',
								$tilesString,
								array(
									'class' => 'gameBoard'
								)
							);
		
	}
	
	//PUBLIC FUNCTION: renderGame
	//Render a full playable game with a whole board and card display and all that
	//beautiful stuff
	public function renderGame( $userUID, $gameInformation ){
	
		//Setup the return string
		$returnString = '';
											
		//Add the necessary javascript and CSS for gameplay
		$returnString .= $this->Html->css('gameplay');
		$returnString .= $this->Html->tag(
										'script',
										$this->gameplayJS()
									);
		//$returnString .= $this->Html->script('Game/unit');
	
		//Render the board
		$returnString .= $this->renderBoard( $gameInformation['Board'] );
		
		//Render the units
		$returnString .= $this->renderUnits( 
												array(
													'userUID' => $userUID
												),
												$gameInformation 
											);
				
		//We didn't do all this work for nothing so let's return the fucking results
		return $returnString;		
		
	}
	
	//PUBLIC FUNCTION: renderTile
	//Render a tile with the given X and Y, we gotta do this shit lots so we might
	//as well create a function for it right
	public function renderTile( $x, $y ){
	
		//Quickly establish whether or not we're making a dark or a light tile by
		//summing the coordinates, even tiles are dark with odd tiles being light
		//Now let's do some psycho analysis to determine how I feel about race
		//relations based on this arbitrary decision shall we?
		$tilesWhite = ( $x + $y ) % 2;
	
		//Establish some basic attributes that we can slam X's and Y's into
		$attributes = array(
						'class' => 'gameTile',
						'light' => $tilesWhite,
						'x'		=> $x,
						'y' 	=> $y
					);
					
		//Now create the HTML and return it
		return $this->Html->tag( 
								'div',
								'',
								$attributes
							);
							
	}
	
	//PUBLIC FUNCTION: renderUnit
	//Render the unit in HTML
	public function renderUnit( $attributes, $movements, $userUnits ){
	
		//Establish the return string
		$returnString = '';
		
		//Create the image of the unit
		$imageString 	= $this->Html->image(
								$attributes['icon']
							);	

		$divString		= $this->Html->tag(
								'div',
								$imageString,
								array(
									'class' => 'gameplayUnit',
									'uid'	=> $attributes['uid']
								)
							);
							
		//Establish the array we're pushing onto
		if( $userUnits ){
			$arrayToPush = 'window.playerUnits';
		}else{
			$arrayToPush = 'window.enemyUnits';
		}
		
		//Establish the movement string
		$movementArrayStrings = '';
		$phpMovementArrayString = array();
		
		//Loop through all the movements
		foreach( $movements as $movementSet ){
			
			//Loop through the direction set movement set movements
			//Yeah, how's that for confusing and poorly structured
			foreach( $movementSet['MovementSet']['Movement'] as $movement ){
						
				//Establish the variables
				$directions			= array();
				$mustMoveAllTheWay 	= $movement['must_move_all_the_way'];
				$priority			= $movement['priority'];
				$spaces 			= $movement['spaces'];
			
				//Set the must move to true or false
				if( ! $mustMoveAllTheWay ){
					$mustMoveAllTheWay = 'false';
				}else{
					$mustMoveAllTheWay = 'true';
				}
				
				//Loop through all the directions
				foreach( $movement['MovementDirectionSet'] as $directionSet ){
					
					//Grab the direction
					foreach( $directionSet['DirectionSet']['DirectionSetDirection'] as $direction ){
				
						//Now that we're all the way in here we add to our array
						$directions[] = array(
							'x' => $direction['Direction']['x'],
							'y'	=> $direction['Direction']['y']
						);
						
					}
					
				}
				
				//Add the movement to the array
				$phpMovementArrayString[$priority] = array(
							'directions'		=> $directions,
							'mustMoveAllTheWay'	=> $mustMoveAllTheWay,	
							'spaces' 			=> $spaces
						);
				
			}
			
		}
		
		$firstTimeThroughMovements = true;
		
		//Loop through the php movement array 
		for( $priority = 1; $priority <= count( $phpMovementArrayString ); $priority++ ){

			//Add the comma except if it's the first time through
			if( $firstTimeThroughMovements ){
				$firstTimeThroughMovements = false;
			}else{
				$movementArrayStrings .= ', ';
			}

			//Add the basics			
			$movementArrayStrings .= 'new movement( '.$phpMovementArrayString[$priority]['mustMoveAllTheWay'].', '.$phpMovementArrayString[$priority]['spaces'].', new Array( ';
			
			//Establish if this is the first element in the array
			$firstTimeThroughDirections = true;
			
			//Add the directions
			foreach( $phpMovementArrayString[$priority]['directions'] as $directionArray ){
				
				//Add the comma except if it's the first time through
				if( $firstTimeThroughDirections ){
					$firstTimeThroughDirections = false;
				}else{
					$movementArrayStrings .= ', ';
				}
				
				//Add the direction to the movement string as an array
				$movementArrayStrings .= 'new Array( '.$directionArray['x'].', '.$directionArray['y'].' )';
				
			}	
			
			//Finish the movement
			$movementArrayStrings .= ' ) )';	
				
		}
		
		$movementsString = 'new Array('.
									$movementArrayStrings .
								')';
							
		//Create the javascript for this unit
		$javascript 	= $this->Html->tag(
								'script',
								$arrayToPush.'.push( new unit( '.
												$attributes['x'].', '.
												$attributes['y'].', "'.
												$attributes['name'].'", '.
												$attributes['defense'].', '.
												$attributes['damage'].', '.
												$attributes['uid'].', '.
												$movementsString .
												') );'
								);
								
		//Add the javascript to the display string
		$returnString = $divString . $javascript;

		return $returnString;
		
	}
	
	//PUBLIC FUNCTION: renderUnits
	//Create a div and render the units in it
	public function renderUnits( $parameters, $gameInformation ){
	
		//Setup the return string
		$unitsString = '';
	
		//Setup some defaults and then destroy them if at all possible
		$userUID = '';
		
		//Replace defaults if possible
		if( isset( $parameters['userUID'] ) ){
			$userUID = $parameters['userUID'];
		}
		
		//Start looping through all of the units each player has in the game
		//IE the user games, so we can get through to their game units
		foreach( $gameInformation['UserGame'] as $userGame ){
			
			//Setup a boolean that indicates whether or not these units belong to
			//the game
			if( $userGame['users_uid'] == $userUID ){
				$usersUnits = true;
			}else{
				$usersUnits = false;
			}
			
			//Loop through the game units
			foreach( $userGame['GameUnit'] as $gameUnit ){
				
				//Renew the attributes
				$attributes = array();
			
				//Grab the game specific information
				$uid			= $gameUnit['uid'];
				$x 				= $gameUnit['x'];
				$y 				= $gameUnit['y'];
				$defense		= $gameUnit['defense'];
				$name 			= $gameUnit['Unit']['UnitType']['name'];
				$damage			= $gameUnit['Unit']['UnitType']['UnitStat']['damage'];
				$unitArtSet 	= $gameUnit['Unit']['UnitArtSet'];
				$movements		= $gameUnit['Unit']['UnitType']['UnitStat']['UnitStatMovementSet'];
				
				//Now we use all this information to make attributes
				$attributes = array_merge(
								$attributes,
								array(
									'x' 		=> $x,
									'y'			=> $y,
									'name'		=> $name,
									'defense'	=> $defense,
									'damage'	=> $damage,
									'uid'		=> $uid
								)
							);
							
				//Grab the display art
				//Loop through each art set icon and grab the icon
				foreach( $unitArtSet['UnitArtSetIcon'] as $artSetIcon ){
					
					if( isset( $artSetIcon['Icon']['image'] ) ){
						
						//Add the attributes
						$attributes = array_merge(
										$attributes,
										array(
											'icon' => $artSetIcon['Icon']['image']
										)
									);
					}
					
				}
				
				//Now we need to render the unit
				$unitsString .= $this->renderUnit( $attributes, $movements, $usersUnits );
				
			}
			
		}
					
		//Now throw it all in a div and return it
		return $this->Html->tag( 
								'div',
								$unitsString,
								array(
									'class' => 'gameplayUnits'
								)
							);
	
	}
	
}

?>