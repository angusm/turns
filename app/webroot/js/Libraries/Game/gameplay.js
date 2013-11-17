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
		
		jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).css({
			"position"	: "absolute", 
			"left"		: nuX + "px",
			"top"		: nuY + "px"
		});
		
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
		if( unitObject.uid == Game_gameplay.selectedUnit.uid ){
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
				//Game_gameplay.processUnitSelection();
				
			}
							
		}
		
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
		jQuery( '.gameplayUnit[uid="' + Game_gameplay.selectedUnit.uid + '"]' ).css({
			"position"	: "absolute", 
			"left"		: nuX + "px",
			"top"		: nuY + "px"
		});
	
	}
	
	//PUBLIC FUNCTION: getGameUpdate
	//Get an update on everything in the game and set things up appropriately
	this.getGameUpdate = function(){
	
		//Make a request to the server and update all the variables
		jQuery.getJSON(
			homeURL + 'Games/getGameUpdate', 
			{
				gameUID: window.gameUID,
				turn:	 Game_gameplay.currentTurn
			},
			function( jSONData ){
								
				//Grab the game update
				window.gameUnits 	= jSONData.gameInformation.GameUnit;

				//Find whose turn it is
				if( jSONData.gameInformation.ActiveUser[0].UserGame.users_uid == window.userUID ){
					window.playersTurn	= true;
				}else{
					window.playersTurn	= false;
				}
				
				//Reset the turn data
				Game_gameplay.resetTurnData();
				
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
		
		//Arrange the board and the pieces
		Game_elements.arrangeTiles();
	
		//Initialized the selected unit
		Game_gameplay.selectedUnit = new Object();
		Game_gameplay.selectedUnit.uid = false;
	
		//Set everything up for the new turn
		Game_gameplay.resetTurnData();
		
	}
	
	//PUBLIC FUNCTION: handleMoveToTile
	//Handle clicks on tiles highlighted for movement
	this.handleMoveToTile = function(){
	
		//We only want to be adding this once per tile
		jQuery( '.highlightedForMove' ).each( function(){
			
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
		if( Game_gameplay.selectedUnit.MovementSet.length == 0 ){
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
		var mustMoveAllTheWay	= Game_gameplay.selectedUnit.MovementSet.Movement[Game_gameplay.selectedUnit.last_movement_priority].mustMoveAllTheWay;
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
	
	//PUBLIC FUNCTION: processUnitSelection
	//Do the work that needs to occur after a unit has been selected.
	this.processUnitSelection = function(){
		
		clickedUnit = jQuery( 'gameplayUnit[uid="' + Game_gameplay.selectedUnit.uid + '"]' );
				
		//Toggle the highlighted and selected units
		Game_gameplay.highlightSelectedUnitPaths();
		Game_gameplay.toggleHighlight( clickedUnit );
		Game_gameplay.toggleSelect( clickedUnit );
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
	
	//PUBLIC FUNCTION: resetTurnData
	//Reset everything as if a unit has never moved
	this.resetTurnData = function(){
		
		//Reposition all the units
		Game_gameplay.setupUnits();
		
		//Reset the highlighted units 
		jQuery( '.highlightedForMove' ).removeClass( 'highlightedForMove' );
	
		//We run a check to see if the user has already selected a unit that it hasn't finished moving
		//If this is the case we select it for the user, otherwise we allow the selection of any unit
		jQuery.each( window.gameUnits, Game_gameplay.checkIfUnitShouldBeSelected );
				
		//If its the player's turn then setup unit selection,
		//otherwise setup a timer to check if it's the user's turn yet
		if( window.playersTurn ){
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
		Game_gameplay.selectedUnit.uid = jQuery( clickedUnit ).attr( 'uid' );
		
		//Loop through the player's units and find the selected unit
		jQuery.each( window.gameUnits, Game_gameplay.checkIfSelected );
		
		Game_gameplay.processUnitSelection();
		
	}
	
	//PUBLIC FUNCTION: setupUnit
	//Setup the given unit
	this.setupUnit = function( unitObjectPosition, unitObject ){
		
		//Align the unit with its tile position
		Game_gameplay.arrangeUnit( unitObject );
		
	}
	
	//PUBLIC FUNCTION: setupUnits
	//Arrange all of the gameplay units
	this.setupUnits = function(){
		
		//Loop through all of the units
		jQuery.each( window.gameUnits,  Game_gameplay.setupUnit );
			
	}
	
	//PUBLIC FUNCTION: toggleHighlight
	//Toggle a highlight effect on the given element
	this.toggleHighlight = function( element ){
		
		//If this unit is highlighted unhighlight it, otherwise unhighlight
		//everything else then highlight it
		if( jQuery( element ).hasClass( 'highlighted' ) ){
			jQuery( element ).removeClass( 'highlighted' );
		}else{
			jQuery( '.highlighted' ).removeClass( 'highlighted' );
			jQuery( element ).addClass( 'highlighted' );
		}
		
	}
	
	//PUBLIC FUNCTION: toggleSelect
	//Toggle the select of the given element
	this.toggleSelect = function( element ){
		
		//Unselect other units and select them
		if( jQuery( element ).hasClass( 'selected' ) ){
			jQuery( element ).removeClass( 'selected' );
		}else{
			jQuery( '.selected' ).removeClass( 'selected' );
			jQuery( element ).addClass( 'selected' );
		}
		
	}
	
	//PUBLIC FUNCTION: unhighlightUnitPaths
	//Remove all the paths for the selected unit
	this.unhighlightUnitPaths = function(){
		
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
	
}