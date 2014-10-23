<?php

//This class will be used to make badass forms just the way I like them

App::uses('AppHelper', 'View/Helper');

class GamePlayHelper extends AppHelper {
	
	//We'll be using some of the HTML helper's functionality to do awesome stuff
  	var $helpers = ['Html'];
	
	//PUBLIC FUNCTION: getArtArray
	//Grab the art array
	public function getArtArray( $unitArtSet=[] ){
		
		//Setup the art array to return
		$artArray = [];
		$artArray['Icon'] 			= [];
		$artArray['CardArtLayer']	= [];
		
		//Add all the icons
		if( isset( $unitArtSet['UnitArtSetIcon'] ) ){
			foreach( $unitArtSet['UnitArtSetIcon'] as $unitArtSetIcon ){
				if( isset( $unitArtSetIcon['Icon']['icon_positions_uid'] ) and isset( $unitArtSetIcon['Icon']['image'] ) and $unitArtSetIcon['Icon']['icon_positions_uid'] == '3' ){
	
					$iconPosition = $unitArtSetIcon['Icon']['icon_positions_uid'];
					$artArray['Icon'][$iconPosition] = $unitArtSetIcon['Icon']['image'];
					
				}
			}
		}
		
		//Add all the card art layers
		if( isset( $unitArtSet['CardArtLayerSet'] ) ){
			foreach( $unitArtSet['CardArtLayerSet'] as $cardArtLayerSet ){
				if( isset( $cardArtLayerSet['CardArtLayer']['image'] ) ){
					$artArray['CardArtLayer'][$cardArtLayerSet['position']] = $cardArtLayerSet['CardArtLayer']['image'];
				}
			}			
		}
		
		return $artArray;
		
	}
	
	//PUBLIC FUNCTION: getMaxTeamCost
	//Return the maximum cost a team is allowed to accrue
	public function getMaxTeamCost(){
	
		$gameConstantModelInstance = ClassRegistry::init( 'GameConstant' );
		$gameConstant = $gameConstantModelInstance->find( 'first' );
		
		return $gameConstant['GameConstant']['max_team_cost'];
		
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
								[
									'class' => 'gameBoard'
								]
							);
		
	}
	
	//PUBLIC FUNCTION: renderGameUnitCard
	//Render a card display
	public function renderGameUnitCard( $gameUnit=[] ){
		
		$cardArtLayerContent 		= '';
		$cardContent				= '';
		$damageBarContent			= '';
		$damageIconContent			= '';
		$defenseBarContent			= '';
		$defenseIconContent			= '';
		$movementClassesContent		= '';
		$movementSelectorsContent	= '';
		$teamcostBarContent			= '';
		$teamcostIconContent		= '';
		$unitTypeName				= '';
		
		//If we have a gameUnit then we need to establish the content
		if( count( $gameUnit ) > 0 ){
			
			//Set the unitTypeName if it's not already set
			if( isset( $gameUnit['GameUnit']['name'] ) ){
				$unitTypeName = $gameUnit['GameUnit']['name'];
			}
			
			//If we have unit art get it in a nice array
			if( isset( $gameUnit['UnitArtSet'] ) ){
				$artArray = $this->getArtArray( $gameUnit['UnitArtSet'] );
			}

			//Loop through all the art and add it to the card art layer content
			foreach( $artArray['CardArtLayer'] as $position => $cardArtLayer ){
				//Add the card art layer
				$cardArtLayerContent .= $this->Html->image( 
											$cardArtLayer, 
											[
												'alt' => 
													'Card Art Layer',
												'cardArtLayerPosition' => 
													$position
											]
										);	
			}
			
			//Establish the damage bar content
			if( isset( $gameUnit['GameUnitStat']['damage'] ) ){
				
				//Damage Value
				$damageValue = $gameUnit['GameUnitStat']['damage'];	
				
				//Loop through the icons and grab the board icon
				if( isset( $artArray['Icon']['6'] ) ){
					for( $damageCounter = 0; $damageCounter < $damageValue; $damageCounter++ ){
						$damageBarContent .= $this->Html->image( 
														$artArray['Icon']['6'], 
														[
															'alt' 	=> 'Damage Point',
															'class'	=> 'attributePoint'
														]
													);	
					}
				}
				$damageBarContent .= $this->Html->tag(
													'div',
													$damageValue,
													[
														'class' => [
															'damageValue',
															'attributeValue'
														]
													]
												);
			}
			//Establish the damage icon content
			//Loop through the icons and grab the board icon
			if( isset( $artArray['Icon']['4'] ) ){
				//Grab the board icon
				$damageIconContent .= $this->Html->image( 
											$artArray['Icon']['4'], 
											[
												'alt' => 'Damage Icon'
											]
										);					
			}
			
			//Establish the damage bar content
			if( isset( $gameUnit['GameUnit']['defense'] ) ){
				
				//Damage Value
				$defenseValue = $gameUnit['GameUnit']['defense'];	
				
				//Loop through the icons and grab the board icon
				if( isset( $artArray['Icon']['7'] ) ){
					for( $defenseCounter = 0; $defenseCounter < $defenseValue; $defenseCounter++ ){
						$defenseBarContent .= $this->Html->image( 
														$artArray['Icon']['7'], 
														[
															'alt' 	=> 'Defense Point',
															'class'	=> 'attributePoint'
														]
													);	
					}
				}
				$defenseBarContent .= $this->Html->tag(
													'div',
													$defenseValue,
													[
														'class' => [
															'defenseValue',
															'attributeValue'
														]
													]
												);
			}
			//Establish the damage icon content
			//Loop through the icons and grab the board icon
			if( isset( $artArray['Icon']['5'] ) ){
				//Grab the board icon
				$defenseIconContent .= $this->Html->image( 
											$artArray['Icon']['5'], 
											[
												'alt' => 'Defense Icon'
											]
										);					
			}
			
			//Establish the teamcost bar content
			if( isset( $gameUnit['GameUnitStat']['teamcost'] ) ){
				
				//Team Cost Value
				$teamcostValue = $gameUnit['GameUnitStat']['teamcost'];	
				
				//Loop through the icons and grab the board icon
				if( isset( $artArray['Icon']['9'] ) ){
					for( $teamcostCounter = 0; $teamcostCounter < $teamcostValue; $teamcostCounter++ ){
						$teamcostBarContent .= $this->Html->image( 
														$artArray['Icon']['9'], 
														[
															'alt' 	=> 'Team Cost Point',
															'class'	=> 'attributePoint'
														]
													);	
					}
				}
				$teamcostBarContent .= $this->Html->tag(
													'div',
													$teamcostValue,
													[
														'class' => [
															'teamcostValue',
															'attributeValue'
														]
													]
												);
			}
			//Establish the damage icon content
			//Loop through the icons and grab the board icon
			if( isset( $artArray['Icon']['12'] ) ){
				//Grab the board icon
				$teamcostIconContent .= $this->Html->image( 
											$artArray['Icon']['12'], 
											[
												'alt' => 'Team Cost Icon'
											]
										);					
			}
			
			
			//We need to create and display all the possible movement sets a 
			//Unit Type might have
			foreach( $gameUnit['GameUnitStat']['GameUnitStatMovementSet'] as $unitStatMovementSet ){
				if( isset( $unitStatMovementSet['MovementSet']['uid'] ) ){

					$movementSet = $unitStatMovementSet['MovementSet'];
			
					//Setup the movement set content
					$movementSetContent = '';
					
					//Grab a name if there is one
					if( isset( $movementSet['name'] ) ){
						$movementSetName = $movementSet['name'];
					}else{
						$movementSetName = '';
					}
					
					//Add the name div
					$movementSetContent .= $this->Html->tag(
													'div',
													$movementSetName,
													[
														'class' => 'movementSetName'
													]
												);
												
					//Add the actual movements
					if( isset( $movementSet['Movement'] ) ){
						foreach( $movementSet['Movement'] as $movement ){
							if( isset( $movement['priority'] ) ){		
							
								//Reset the movement content for the new movement
								$movementContent = '';
												
								//Establish the movement box image
								if( isset( $artArray['Icon']['8'] ) ){
									
									//Display the movement set background icon
									$movementContent .= $this->Html->image( 
																	$artArray['Icon']['8'], 
																	[
																		'alt' 		=> 	'Movement Box',
																		'class'		=>	'movementBox'
																	]
																);
								}
							
								//Display the spaces that can be covered in this move
								if( isset( $movement['spaces'] ) ){
									$movementContent .= $this->Html->tag(
																	'div',
																	$movement['spaces'],
																	[
																		'class' => 'movementSpaces'
																	]
																);
								}
																
							
								//Setup a display div for all the arrows
								$movementArrowsContent = '';
								
								//Grab all the movement direction set
								if( isset( $movement['MovementDirectionSet'] ) ){
									foreach( $movement['MovementDirectionSet'] as $movementDirectionSet ){
										if( isset( $movementDirectionSet['DirectionSet']['DirectionSetDirection'] ) ){
											foreach( $movementDirectionSet['DirectionSet']['DirectionSetDirection'] as $directionSetDirection ){
												
												if( isset( $directionSetDirection['Direction'] ) ){
													
													//Get a nicer variable name to work with
													$direction = $directionSetDirection['Direction'];
													
													//Check to make sure we got the info we need
													if( isset( $direction['name'] ) and isset( $direction['x'] ) and isset( $direction['y'] ) and isset( $artArray['Icon']['11'] ) ){
													
														//Toss up the image, storing its gameplay information in the
														//HTML
														echo $this->Html->image( $artArray['Icon']['11'], 
																				[
																					'alt' 		=> 	$direction['name'] . ' Movement Arrow',
																					'x'			=>  $direction['x'],
																					'y'			=> 	$direction['y'],
																					'class'		=> 	'movementArrow' . $direction['name'],
																					'direction'	=>	$direction['name']
																				]);
													
													}
													
												}
												
											}
										}
									}
								}
							
								$movementContent .= $this->Html->tag(
																'div',
																$movementArrowsContent,
																[
																	'class' => 'movmentArrows'
																]
															);
					
								$movementSetContent .= $this->Html->tag(
																	'div',
																	$movementContent,
																	[
																		'class'		=> 'movement',
																		'priority'  => $movement['priority']
																	]
																);
							}
						}
					}
					
					//Setup the div to hold this movement set
					$movementClassesContent .= $this->Html->tag(
														'div',
														$movementSetContent,
														[
															'class' 			=> 'movementSet',
															'movementSetUID'	=> $movementSet['uid']
														]
													);
													
				}
			}
			
			
			//For each movement set, toss out an icon that can be used to
			//select it
			if( isset( $gameUnit['GameUnitStat']['GameUnitStatMovementSet'] ) ){
				foreach( $gameUnit['GameUnitStat']['GameUnitStatMovementSet'] as $unitStatMovementSet ){
					if( isset( $unitStatMovementSet['MovementSet']['uid'] ) and isset( $artArray['Icon']['11'] ) ){
	
						$movementSet = $unitStatMovementSet['MovementSet'];
						$movementSelectorsContent .= $this->Html->tag(
																'div',
																$this->Html->image(
																				$artArray['Icon']['11'],
																				[
																					'alt'	=> 'Movement Set Selector Icon',
																					'class'	=>  'movementSetSelector'
																				]
																			),
																[
																	'class' => 'movementSetSelector'
																]
															);
					}
				}
			}					
		
		}

		//Add all the content to their divs
		
		//Add the card art layers to their div
		$cardArtLayerDiv 	= $this->Html->tag(
									  	'div',
									  	$cardArtLayerContent,
										[
											'class' => 'cardArtLayers'
									  	]
								  	);

        //Add the damage icon to its div
        $damageIconDiv		= $this->Html->tag(
            'div',
            $damageIconContent,
            [
                'class' => [
                    'damageIcon',
                    'attributeIcon'
                ]
            ]
        );
		
		//Add the damage bar content to its div
		$damageBarDiv 		= $this->Html->tag(
										'div',
										$damageBarContent .
                                        $damageIconDiv,
										[
											'class' => [
												'damageBar',
												'attributeBar'
											]
										]
									);

        //Add the defense icon to its div
        $defenseIconDiv		= $this->Html->tag(
            'div',
            $defenseIconContent,
            [
                'class' => [
                    'defenseIcon',
                    'attributeIcon'
                ]
            ]
        );
		
		//Add the defense bar content to its div
		$defenseBarDiv 		= $this->Html->tag(
										'div',
										$defenseBarContent .
                                        $defenseIconDiv,
										[
											'class' => [
												'defenseBar',
												'attributeBar'
											]
										]
									);

        //Add the teamcost icon to its div
        $teamcostIconDiv		= $this->Html->tag(
            'div',
            $teamcostIconContent,
            [
                'class' => [
                    'teamcostIcon',
                    'attributeIcon'
                ]
            ]
        );
		
		//Add the teamcost bar content to its div
		$teamcostBarDiv 		= $this->Html->tag(
										'div',
										$teamcostBarContent .
                                        $teamcostIconDiv,
										[
											'class' => [
												'teamcostBar',
												'attributeBar'
											]
										]
									);
									
		//Unit stat box
		$unitStatBoxDiv			= $this->Html->tag(
										'div',
										$this->Html->tag(
											'div',
											$unitTypeName,
											[
												'class' => 'unitTypeName'
											]
										).
                                        $damageBarDiv   .
                                        $defenseBarDiv  .
                                        $teamcostBarDiv,
										[
											'class' => 'unitStatBox'
										]
									);
									
		//Setup the movement classes div
		$movementClassesDiv		= $this->Html->tag(
										'div',
										$movementClassesContent,
										[
											'class' => 'movementClasses'
										]
									);
									
		//Setup the movement selectors div
		$movementSelectorsDiv	= $this->Html->tag(
										'div',
										$movementSelectorsContent,
										[
											'class' => 'movementSelectors'
										]
									);
    
		//Add all of the card content    
		$cardContent .= $cardArtLayerDiv;
		$cardContent .= $unitStatBoxDiv;
		$cardContent .= $movementClassesDiv;
		$cardContent .= $movementSelectorsDiv;

		//Throw everything in the card content string into the div
		$cardString 		= $this->Html->tag(
									'div',
									$cardContent,
									[
										'class' => 'unitCard'
									]
								);
		
		return $cardString;
		
		
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
											[
												'userUID' => $userUID
											],
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
		$attributes = [
						'class' => 'gameTile',
						'light' => $tilesWhite,
						'x'		=> $x,
						'y' 	=> $y
					];
					
		//Now create the HTML and return it
		return $this->Html->tag( 
								'div',
								'',
								$attributes
							);
							
	}
	
	//PUBLIC FUNCTION: renderUnit
	//Render the unit in HTML
	public function renderUnit( $gameUnit, $icon ){
			
		//Grab some info from the unit
		$userUID 	= $gameUnit['users_uid'];
		$uid		= $gameUnit['uid'];
		$damage		= $gameUnit['damage'];
		$defense	= $gameUnit['defense'];
			
		//Create the image of the unit
		$imageString 	= $this->Html->image(
								$icon
							);	
							
		//Create the div that'll contain the unit's defense
		$defenseDiv = $this->Html->tag(
									'div',
									$defense,
									[
										'class' => 'gameplayUnitDefense'
									]
								);
		
		//Create the div that'll contain the unit's attack
		$attackDiv = $this->Html->tag(
									'div',
									$damage,
									[
										'class' => 'gameplayUnitAttack'
									]
								);

		//Link together the final contents
		$finalContents = '';
		$finalContents .= $imageString;
		$finalContents .= $defenseDiv;
		$finalContents .= $attackDiv;

		//Establish the return string
		$returnString	= $this->Html->tag(
								'div',
								$finalContents,
								[
									'users_uid'	=> $userUID,
									'class' 	=> 'gameplayUnit',
									'uid'		=> $uid
								]
							);
								
		return $returnString;
		
	}
	
	//PUBLIC FUNCTION: renderUnits
	//Create a div and render the units in it
	public function renderUnits( $parameters, $gameInformation ){
	
		//Setup the return string
		$unitsString = '';
		
		//If 'GameUnit' is undefined just create a blank
		//There are times when we'll want to render units without
		//having any units to render
		
		if( isset( $gameInformation['GameUnit'] ) and isset( $gameInformation['ActiveUser'][0]['UserGame']['users_uid'] ) ){
				
			//Check to see if the current user is the active player
			if( $gameInformation['ActiveUser'][0]['UserGame']['users_uid'] == $parameters['userUID'] ){
				$playersTurn = 'true';	
			}else{
				$playersTurn = 'false';
			}
				
			//Add a json encoded version of the gameUnits to the $unitsString
			$unitsString .= $this->Html->tag( 
									'script',
									'var currentTurn		= ' . $gameInformation['Game']['turn']						. ';'.
									'var selectedUnitUID	= ' . intval($gameInformation['Game']['selected_unit_uid'])	. ';'.
									'var gameUID			= ' . $gameInformation['Game']['uid']						. ';'.
									'var gameUnits 			= ' . json_encode( $gameInformation['GameUnit'] ) 			. ';'.
									'var playersTurn		= ' . $playersTurn											. ';'.
									'var userUID   			= ' . $parameters['userUID'] 								. ';',
									[]
								);
				
			//Loop through the game units
			foreach( $gameInformation['GameUnit'] as $gameUnit ){
												
				//Grab the display art
				//Loop through each art set icon and grab the icon
				foreach( $gameUnit['UnitArtSet']['UnitArtSetIcon'] as $artSetIcon ){
					
					if( isset( $artSetIcon['Icon']['image'] ) ){
						//Add the attributes
						$iconImageURL = $artSetIcon['Icon']['image'];
						break;
					}
					
				}
					
				//Now we need to render the unit
				$unitsString .= $this->renderUnit( $gameUnit, $iconImageURL );
				
			}
			
		}
		
		//Now throw it all in a div and return it
		return $this->Html->tag( 
								'div',
								$unitsString,
								[
									'class' => 'gameplayUnits'
								]
							);
	
	}
	
}