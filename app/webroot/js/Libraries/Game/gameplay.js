// JavaScript Document

//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all
var Game_elements = null;

var loadDependenciesFor_Game_gameplay = function(){

	libraries.push( new Array( 'Game', 'elements' ) );
	
}

//DOCUMENT READY
//When the document is fully ready, call the main function
jQuery(document).ready( function(){

	Game_elements = new elements();
	Game_gameplay = new gameplay();
	Game_gameplay.handleEverything();

});

//Alright let's do this matchmaking stuff
function gameplay(){	
	
	//PUBLIC FUNCTION: arrangeUnit
	//Arrange the unit
	this.arrangeUnit = function( unitObject ){
			
		var nuX = unitObject.x * 70;
		var nuY = unitObject.y * 70;
		
		jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).animate(
																{
																	"position"	: "absolute", 
																	"left"		: nuX + "px",
																	"top"		: nuY + "px"
																},
																1000
																);
		
	}
	
	this.checkForUpdates = function(){
		window.setTimeout(function() {
     		Game_gameplay.getGameUpdate();
   		}, 1000);
	}

	
	//PUBLIC FUNCTION: checkIfSelected
	//See if the given unit is selected, if it is set the position
	this.checkIfSelected = function( unitObjectPosition, unitObject ){
		
		//Compare the unit object UID with the selected unit UID	
		if( unitObject.uid == window.selectedUnitUID ){
			Game_gameplay.selectedUnit = unitObject;
		}
		
	}
	
	//PUBLIC FUNCTION: checkIfUnitShouldBeSelected
	//Check if the unit should be selected, if it should be
	//selected then select it.
	this.checkIfUnitShouldBeSelected = function( unitObjectPosition, unitObject ){
	
		if( unitObject.last_movement_priority != "0" ){
			
			//If the unti belongs to the current player then process it's selection
			if( unitObject.users_uid == window.userUID ){
				
				//Set the selected unit info
				Game_gameplay.selectedUnit = unitObject;
				
			}
							
		}
		
	}
	
	//PUBLIC FUNCTION: clearEverything
	//Clear everything from the previous turn
	this.clearEverything = function(){
		
		//Reset the highlighted units 
		jQuery( '.highlightedForMove' ).removeClass( 'highlightedForMove' );
		
		//Clear the selected unit DOM's class		
		Game_gameplay.clearSelectedUnit();
		Game_gameplay.unhighlightUnitPaths();
		Game_gameplay.unhighlightEverything();
				
	}
		
	//PUBLIC FUNCTION: clearSelectedUnit
	//Clear the selection of the given unit
	this.clearSelectedUnit = function(){
		
		//Unselect other units and select them
		jQuery( '.selected' ).removeClass( 'selected' );
		jQuery( 'highlighted' ).removeClass( 'highlighted' );
		
	}
	
	//PUBLIC FUNCTION: colorUnits
	//Color the units according to the player
	this.colorUnits = function(){
		
		//Loop through all the units
		jQuery.each( window.gameUnits, function( unitPos, unitObject ){
			
			//Color the unit according to whose side it's on
			if( unitObject.users_uid == window.userUID ){
				jQuery('.gameplayUnit[uid="'+unitObject.uid+'"] > img').pixastic("coloradjust", {red:0,green:0,blue:0.2});
			}else{
				jQuery('.gameplayUnit[uid="'+unitObject.uid+'"] > img').pixastic("coloradjust", {red:0.2,green:0,blue:0});
			}
			
		});
	
	}
	
	//PUBLIC FUNCTION: disallowSelectChange
	//Remove the ability to select any new units or deselect a currently
	//selected unit
	this.disallowSelectChange = function(){
	
		//We only want to be removing this once
		jQuery( '.gameplayUnit' ).each( function(){
			
			if( jQuery(this).isBound( 'click', Game_gameplay.selectUnit ) ){
				jQuery(this).unbind( 
					'click',
					Game_gameplay.selectUnit
				);
			}
			
		});
		
		
	}
	
	//PUBLIC FUNCTION: endGame
	//End the game
	this.endGame = function(){
		
		alert( 'GAME OVER' );
		
	}
	
	//PUBLIC FUNCTION: executeMove
	//Adjust the stats and display to reflect the move
	this.executeMove = function( tileMovedTo ){
		
		//Grab the new logical X and Y
		var nuLogicalX = jQuery( tileMovedTo ).attr('x');
		var nuLogicalY = jQuery( tileMovedTo ).attr('y');
		var movementSetToLockIn = jQuery( tileMovedTo ).attr( 'movementSet' );
		
		var movementSetArray = Game_gameplay.selectedUnit.MovementSet;
		
		Game_gameplay.selectedUnit.movements = new Array( movementSetArray );
		
		//Push this move to the server
		Game_gameplay.pushMoveToServer( nuLogicalX, nuLogicalY );
				
		//Change the data of the selected unit
		Game_gameplay.selectedUnit.x = parseInt( nuLogicalX );
		Game_gameplay.selectedUnit.y = parseInt( nuLogicalY );
		
		//Calculate the new x and y pixel position
		var nuX = Game_gameplay.selectedUnit.x * 70;
		var nuY = Game_gameplay.selectedUnit.y * 70;
		
		//Increase the selected unit move position
		Game_gameplay.selectedUnit.last_movement_priority++;

		//Move the unit visually
		Game_gameplay.setupUnit( 1, Game_gameplay.selectedUnit );
	
	}
	
	//PUBLIC FUNCTION: getGameUpdate
	//Get an update on everything in the game and set things up appropriately
	this.getGameUpdate = function(){
	
		//Make a request to the server and update all the variables
		jQuery.getJSON(
			homeURL + 'Games/getGameUpdate', 
			{
				gameUID: 		 window.gameUID,
				lastKnownTurn:	 window.currentTurn
			},
			function( jSONData ){
								
				if( jSONData.gameInformation == false ){
					Game_gameplay.getGameUpdate()
				}else{
					
					//Process the game update			
					Game_gameplay.processGameUpdate( jSONData );
						
						console.log( jSONData );
						
						window.asdf = jSONData;
						
					//Check if the game's over
					if( jSONData.gameInformation.game_over == true ){
						Game_gameplay.endGame();
					}
					
				}
					
			}
		).done( 
			function(){
			}
		).fail( 
			function(data){
			}
		).always(
			function(){
			}
		);
			
		
	}
	
	//PUBLIC FUNCTION: handleEverything
	//Just be Pepper Potts already, do everything
	this.handleEverything = function(){
		
		//Setup the unit colors
		Game_gameplay.colorUnits();
		
		//Set the selectedUnit to a default
		Game_gameplay.resetSelectedUnit();
				
		//Arrange the board and the pieces
		Game_elements.arrangeStaticElements();
	
		//Set everything up for the new turn
		Game_gameplay.resetTurnData();
		
	}
	
	//PUBLIC FUNCTION: handleMoveToTile
	//Handle clicks on tiles highlighted for movement
	this.handleMoveToTile = function(){
		
		
		//Clear out any old event listeners
		jQuery( '.gameTile' ).each( function(){
			
			if( jQuery(this).isBound( 'click', Game_gameplay.moveSelectedUnitToTile ) ){
				jQuery(this).unbind( 
					'click',
					Game_gameplay.moveSelectedUnitToTile
				);
			}
			
		});
	
		//We only want to be adding this once per tile
		jQuery( '.highlightedForMove' ).each( function(){
			
			var highlightedTile = jQuery(this);
			
			//Check to see if there are any units that are on the same space as one
			//of the highlighted tiles. If there are then add a listener on the unit to
			//move the selected Unit onto its tile.
			jQuery.each( window.gameUnits, function( unitIndex, unitObject ){
								
				if( parseInt(unitObject.x) == parseInt(highlightedTile.attr('x')) && parseInt(unitObject.y) == parseInt(highlightedTile.attr('y')) ){
					
					//We still only want to add a listener to this unit once
					var unitElement = jQuery('div.gameplayUnit[uid="'+unitObject.uid+'"]');
					
					if( unitObject.defense > 0 && ! unitElement.isBound( 'click', Game_gameplay.moveSelectedUnitToUnit ) ){
						
						unitElement.bind(
							'click',
							Game_gameplay.moveSelectedUnitToUnit
						);	
					}
				}
			});
			
			//Add a listener on the tile itself
			if( ! jQuery(this).isBound( 'click', Game_gameplay.moveSelectedUnitToTile ) ){
				jQuery(this).bind( 
					'click',
					Game_gameplay.moveSelectedUnitToTile
				);
			}
			
		});
		
	}
	
	//PUBLIC FUNCTION: handleSelectionOfAnyUnit
	//Setup a function to handle the selection of any of the player's units	
	this.handleSelectionOfAnyUnit = function(){
	
		//We only want to be adding this once
		jQuery( '.gameplayUnit[users_uid="'+window.userUID+'"]' ).each( function(){
			
			if( ! jQuery(this).isBound( 'click', Game_gameplay.selectUnit ) ){
				jQuery(this).bind( 
					'click',
					Game_gameplay.selectUnit
				);
			}
			
		});
		
	}
	
	//PUBLIC FUNCTION: handleUnitSelection
	//Handle selecting the unit when its clicked on
	this.handleUnitSelection = function(){
		
		//If we still don't have a selected unit then allow the user to select any of their units
		if( Game_gameplay.selectedUnit.uid != false ){
			Game_gameplay.processUnitSelection();	
		}else{
			Game_gameplay.handleSelectionOfAnyUnit();
		}
	
	}
	
	//PUBLIC FUNCTION: highlightSelectedUnitPaths
	//Highlight the spaces the unit can move to
	this.highlightSelectedUnitPaths = function(){
		
		//Check and see if the selected unit has a currently selected MovementSet,
		//if it does then that's the one we have to move with, otherwise the 
		//user has options
		if( Game_gameplay.selectedUnit.MovementSet.Movement === undefined ){
			Game_gameplay.selectedUnit.MovementSet = Game_gameplay.selectedUnit.GameUnitStat.GameUnitStatMovementSet[0].MovementSet;
		}
		
		//If we haven't locked in a movement set then loop through all the movement sets
		//and light up their paths, otherwise only show paths for the currently selected
		//movement set
		var availableMovementDirectionSets = Game_gameplay.selectedUnit.MovementSet.Movement[Game_gameplay.selectedUnit.last_movement_priority].MovementDirectionSet;
		
		//Loop through all the movements for the current move position
		jQuery.each( 
			availableMovementDirectionSets, 
			function( movementDirectionSetPos, movementDirectionSet ){
				jQuery.each( 
					movementDirectionSet.DirectionSet.DirectionSetDirection,
					Game_gameplay.highlightUnitPath 
				);
			}
		);
		
	}
	
	//PUBLIC FUNCTION: highlightUnitPath
	//Highlight an available path
	this.highlightUnitPath = function( directionPosition, direction ){
		
		//Add the angle of the previous movement to the direction of following moves
		direction = parseInt(direction.Direction.angle) + parseInt(Game_gameplay.selectedUnit.last_movement_angle);
		
		//Grab the stats
		var finalX				= parseInt(Game_gameplay.selectedUnit.x);
		var finalY				= parseInt(Game_gameplay.selectedUnit.y);
		var mustMoveAllTheWay	= Game_gameplay.selectedUnit.MovementSet.Movement[Game_gameplay.selectedUnit.last_movement_priority].must_move_all_the_way;
		var spaces 	  			= Game_gameplay.selectedUnit.MovementSet.Movement[Game_gameplay.selectedUnit.last_movement_priority].spaces;
		var xDirection 			=   parseInt( Math.round( Math.sin( direction * (Math.PI / 180) ) ) );
		var yDirection 			= - parseInt( Math.round( Math.cos( direction * (Math.PI / 180) ) ) );
		
		//Figure out the tiles to light up
		//If we have to move all the way we only have one tile to light up
		if( mustMoveAllTheWay ){
			
			finalX	+= ( spaces * xDirection );
			finalY	+= ( spaces * yDirection );
		
			jQuery( '.gameTile[x="'+finalX+'"][y="'+finalY+'"]' ).addClass( 'highlightedForMove' );
			jQuery( '.gameTile[x="'+finalX+'"][y="'+finalY+'"]' ).attr( 'movementSet', Game_gameplay.currentMovementSet );

		//If it's not a must move all the way move then we light up every tile
		//along the way
		}else{
			
			//Loop through the spaces
			for( spaceCounter = 1; spaceCounter <= spaces; spaceCounter++ ){
			
				finalX = parseInt(Game_gameplay.selectedUnit.x) + ( spaceCounter * xDirection );
				finalY = parseInt(Game_gameplay.selectedUnit.y) + ( spaceCounter * yDirection );
				
				jQuery( '.gameTile[x="'+finalX+'"][y="'+finalY+'"]' ).addClass( 'highlightedForMove' );
				jQuery( '.gameTile[x="'+finalX+'"][y="'+finalY+'"]' ).attr( 'movementSet', Game_gameplay.currentMovementSet );
				
			}
			
		}
		
	}
	
	//PUBLIC FUNCTION: killUnit
	//Kill the given unit
	this.killUnit = function( unitObject ){
			 
		//Grab the current width, height, x and y
		var startHeight	= parseInt( jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).css( 'height' ) );
		var startWidth	= parseInt( jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).css( 'width' )	);
		var startX 		= parseInt( jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).css( 'left' )	);
		var startY 		= parseInt( jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).css( 'top' ) 	);
		
		//Calculate the scaling
		var scale 		= 2;
		var endHeight	= startHeight 	* scale;
		var endWidth	= startWidth 	* scale;
		var endX		= startX 		- ( startWidth  / scale );
		var endY		= startY		- ( startHeight / scale );
			 
		//Make the unit disappear
		jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).animate(
																{
																	"position"	: "absolute", 
																	"left"		: endX + "px",
																	"top"		: endY + "px",
																	"width"		: endWidth + "px",
																	"height"	: endHeight + "px",
																	"opacity"	: 0
																},
																1000,
																'swing',
																function(){
																	jQuery(this).remove();
																}
															);
	
			
		
	}
	
	//PUBLIC FUNCTION: moveSelectedUnitToTile
	//When a highlighted tile is clicked the selected unit should be moved to it
	this.moveSelectedUnitToTile = function( triggeredEvent ){
			
		//Disable all of the other units from being selected
		Game_gameplay.disallowSelectChange();
		
		//Reset the highlighted units 
		Game_gameplay.unhighlightUnitPaths();
		
		//Grab the moved to tile and perform the move
		var tileMovedTo = triggeredEvent.target;
		Game_gameplay.executeMove( tileMovedTo );
				
				
	}
	
	//PUBLIC FUNCTION: moveSelectedUnitToUnit
	//Move the selected unit to the position of the unselected unit
	this.moveSelectedUnitToUnit = function( triggeredEvent ){
			
		//Grab the x and y of the game unit that was the target of this event
		var unitElement = jQuery( triggeredEvent.target ).closest( '.gameplayUnit' );
		
		jQuery.each( window.gameUnits, function( unitIndex, unitObject ){
			
			if( jQuery(unitElement).attr('uid') == unitObject.uid ){
				
				//Grab the targeted X and Y
				var targetedX = unitObject.x;
				var targetedY = unitObject.y;
				
				//Trigger the event on the tile the targeted unit sits on
				jQuery( 'div.gameTile[x="'+targetedX+'"][y="'+targetedY+'"]' ).click();
				
			}
			
		});
		
	}
	
	//PUBLIC FUNCTION: processGameUpdate
	//Handle game update
	this.processGameUpdate = function( jSONData ){
		
		//Set the new turn 
		window.currentTurn = jSONData.gameInformation.Game.turn;
										
		//Grab the game update
		window.gameUnits 		= jSONData.gameInformation.GameUnit;
		window.selectedUnitUID 	= jSONData.gameInformation.Game.selected_unit_uid;
		
		if( window.selectedUnitUID == null ){
			Game_gameplay.resetSelectedUnit();
		}

		//Find whose turn it is
		if( jSONData.gameInformation.ActiveUser[0].UserGame.users_uid == window.userUID ){
			window.playersTurn	= true;
		}else{
			window.playersTurn	= false;
		}
		
		//Reset the turn data
		Game_gameplay.resetTurnData();
		
	}
	
	//PUBLIC FUNCTION: processUnitSelection
	//Do the work that needs to occur after a unit has been selected.
	this.processUnitSelection = function(){
		
		//Clear everything
		Game_gameplay.clearEverything();
		
		//Grab the selected unit DOM element
		selectedUnitDOMElement = jQuery( '.gameplayUnit[uid="' + Game_gameplay.selectedUnit.uid + '"]' );
				
		//Toggle the highlighted and selected units
		Game_gameplay.highlightSelectedUnitPaths();
		jQuery( selectedUnitDOMElement ).addClass( 'selected' );
		jQuery( selectedUnitDOMElement ).addClass( 'highlighted' );
		Game_gameplay.handleMoveToTile();
		
	}
	
	//PUBLIC FUNCTION: pushMoveToServer
	//Push the selected move the unit has made to the server
	//Then if it's successful we'll proceed to finalize the move
	this.pushMoveToServer = function( nuX, nuY ){
		
		//Make the call to the server
		jQuery.getJSON(
			homeURL + 'Games/processUnitMove', 
			{
				gameUnitUID:	Game_gameplay.selectedUnit.uid,
				x:				nuX,
				y:				nuY
			},
			function( jSONData ){
				Game_gameplay.getGameUpdate();
			}
		).done( 
			function(){
			}
		).fail( 
			function(data){
			}
		).always(
			function(){
			}
		);
		
	}
	
	//PUBLIC FUNCTION: resetSelectedUnit
	//Reset the selected unit
	this.resetSelectedUnit = function(){
	
		Game_gameplay.selectedUnit 		= new Object();
		Game_gameplay.selectedUnit.uid 	= false;
		
	}
	
	//PUBLIC FUNCTION: resetTurnData
	//Reset everything as if a unit has never moved
	this.resetTurnData = function(){
		
		//Reposition all the units
		Game_gameplay.setupUnits();
		
		//Clear everything
		Game_gameplay.clearEverything();
		
		//Grab the selected unit		
		jQuery.each( window.gameUnits, Game_gameplay.checkIfSelected );
		
		//If its the player's turn then setup unit selection,
		//otherwise setup a timer to check if it's the user's turn yet
		if( window.playersTurn ){
			//Handle selecting of the player's units
			Game_gameplay.handleUnitSelection();
		}else{
			//Setup callback timer to get game updates
			Game_gameplay.checkForUpdates();
		}
		
	}
	
	//PUBLIC FUNCTION: selectUnit
	//Select the given unit
	this.selectUnit = function( triggeredEvent ){
	
		//Click unit
		var clickedUnit = jQuery( triggeredEvent.target ).closest( '.gameplayUnit' );
		
		//Grab the UID from the unitElement 
		window.selectedUnitUID = jQuery( clickedUnit ).attr( 'uid' );
		
		//Loop through the player's units and check if there is a unit with
		//a move already in progress and select that Unit instead.
		jQuery.each( window.gameUnits, Game_gameplay.checkIfSelected );
		
		Game_gameplay.processUnitSelection();
		
	}
	
	//PUBLIC FUNCTION: setupUnit
	//Setup the given unit
	this.setupUnit = function( unitObjectPosition, unitObject ){

		//Set the unit's defense and damage display
		Game_gameplay.updateUnitStats( unitObject );

		//If the unit is still alive, move it into position
		if( unitObject.defense > 0 ){
						
			//Align the unit with its tile position
			Game_gameplay.arrangeUnit( unitObject );
		
		//If the unit isn't alive, kill it	
		}else{
			
			//Kill it
			Game_gameplay.killUnit( unitObject );
			
		}
		
	}
	
	//PUBLIC FUNCTION: setupUnits
	//Arrange all of the gameplay units
	this.setupUnits = function(){
		
		//Loop through all of the units
		jQuery.each( window.gameUnits,  Game_gameplay.setupUnit );
			
	}
	
	//PUBLIC FUNCTION: unhighlightEverything
	//Unhighlight everything that has been highlighted
	this.unhighlightEverything = function(){
	
		jQuery( '.highlighted' ).removeClass( 'highlighted' );	
		jQuery( '.highlightedForMove' ).removeClass( 'highlightedForMove' );
		
	}
	
	//PUBLIC FUNCTION: unhighlightUnitPaths
	//Remove all the paths for the selected unit
	this.unhighlightUnitPaths = function(){
		
		//Remove the event listeners from any units
		jQuery( '.gameplayUnit' ).each( function(){
			
			if( jQuery(this).isBound( 'click', Game_gameplay.moveSelectedUnitToUnit ) ){
				jQuery(this).unbind(
					'click',
					Game_gameplay.moveSelectedUnitToUnit
				);	
			}
			
		});
		
		//Remove the event listener for the highlighted tiles
		jQuery( '.highlightedForMove' ).each( function(){
			
			if( jQuery(this).isBound( 'click', Game_gameplay.moveSelectedUnitToTile ) ){
				jQuery(this).unbind( 
					'click',
					Game_gameplay.moveSelectedUnitToTile
				);
			}
			
		});
		
		//Remove the highlighted class
		jQuery( '.highlightedForMove' ).removeClass( 'highlightedForMove' );
		
	}
	
	//PUBLIC FUNCTION: updateUnitStats
	//Update the unit's statistics as they are displayed on its game tile
	this.updateUnitStats = function( unitObject ){
		
		//For this unit set its damage and defense
		jQuery( '.gameplayUnit[uid="'+unitObject.uid+'"] > .gameplayUnitAttack' ).html( unitObject.damage );
		jQuery( '.gameplayUnit[uid="'+unitObject.uid+'"] > .gameplayUnitDefense' ).html( unitObject.defense );
		
	}
	
}