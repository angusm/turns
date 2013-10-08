<?php

//This class will be used to make badass forms just the way I like them

App::uses('AppHelper', 'View/Helper');

class GamePlayHelper extends AppHelper {
	
	//We'll be using some of the HTML helper's functionality to do awesome stuff
  	var $helpers = array('Html');
	
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
	public function renderUnit( $attributes ){
	
		//Establish the return string
		$returnString = '';
		
		//Create the image of the unit
		$imageString 	= $this->Html->image(
								$attributes['icon']
							);	

		$divString		= $this->Html->tag(
								'div',
								$imageString,
								array_merge(
									$attributes,
									array(
										'class' => 'gameplayUnit'
									)
								)
							);

		return $divString;
		
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
									'damage'	=> $damage
								)
							);
							
				//Grab the display art
				//foreach( $unitArtSet as $artSet ){
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
				//}
				
				//Now we need to render the unit
				$unitsString .= $this->renderUnit( $attributes );
				
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