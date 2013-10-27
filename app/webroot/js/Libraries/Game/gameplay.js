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
	
	var angleOfLastMove				= 0;
	var currentMovementSet			= 0;
	var selectedUnitMovePosition	= 0;
	var selectedUnit		 		= null;
	var selectedUnitUID				= -1;
	
	
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
	
	//PUBLIC FUNCTION: checkIfSelected
	//See if the given unit is selected, if it is set the position
	this.checkIfSelected = function( unitObjectPosition, unitObject ){
	
		//Compare the unit object UID with the selected unit UID	
		if( unitObject.uid == Game_gameplay.selectedUnitUID ){
		
			Game_gameplay.selectedUnit = unitObject;
			
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
	
	//PUBLIC FUNCTION: dontRotate
	//Just leave the x and y alone
	this.dontRotate = function( originalX, originalY ){
	
		return new Array( orginalX, orginalY );	
		
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
		
		//Match the closest angle that's a multiple of 45 and store it as the
		//last move angle
		Game_gameplay.angleOfLastMove = Math.round( 
											Math.atan2(
												nuLogicalX - Game_gameplay.selectedUnit.x, 
												nuLogicalY - Game_gameplay.selectedUnit.y
											) * 180 / Math.PI 
										);
		
		//Change the data of the selected unit
		Game_gameplay.selectedUnit.x = parseInt( nuLogicalX );
		Game_gameplay.selectedUnit.y = parseInt( nuLogicalY );
		
		//Calculate the new x and y pixel position
		var nuX = Game_gameplay.selectedUnit.x * 70;
		var nuY = Game_gameplay.selectedUnit.y * 70;
		
		//Increase the selected unit move position
		Game_gameplay.selectedUnitMovePosition++;

		//Move the unit visually
		jQuery( '.gameplayUnit[uid="' + Game_gameplay.selectedUnit.uid + '"]' ).css({
			"position"	: "absolute", 
			"left"		: nuX + "px",
			"top"		: nuY + "px"
		});
	
	}
	
	//PUBLIC FUNCTION: handleEverything
	//Just be Pepper Potts already, do everything
	this.handleEverything = function(){
		Game_elements.arrangeTiles();
		Game_gameplay.setupUnits();
		Game_gameplay.handleUnitSelection();
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
	
	//PUBLIC FUNCTION: handleUnitSelection
	//Handle selecting the unit when its clicked on
	this.handleUnitSelection = function(){
	
		//We only want to be adding this once
		jQuery( '.gameplayUnit' ).each( function(){
			
			if( ! jQuery(this).isBound( 'click', Game_gameplay.selectUnit ) ){
				jQuery(this).bind( 
					'click',
					Game_gameplay.selectUnit
				);
			}
			
		});
		
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
		var availableMovementDirectionSets = Game_gameplay.selectedUnit.MovementSet.Movement[Game_gameplay.selectedUnitMovePosition].MovementDirectionSet;
		
		//Loop through all the movements for the current move position
		jQuery.each( 
			availableMovementDirectionSets, 
			function( movementDirectionSetPos, movementDirectionSet ){
				jQuery.each( 
					movementDirectionSet.DirectionSet.DirectionSetDirection,
					Game_gameplay.highlightUnitPath 
				);
			});
		
	}
	
	//PUBLIC FUNCTION: highlightUnitPath
	//Highlight an available path
	this.highlightUnitPath = function( directionPosition, direction ){
		
		//Add the angle of the previous movement to the direction of following moves
		direction = parseInt(direction.Direction.angle) + Game_gameplay.angleOfLastMove;
		
		//Grab the stats
		var finalX				= parseInt(Game_gameplay.selectedUnit.x);
		var finalY				= parseInt(Game_gameplay.selectedUnit.y);
		var mustMoveAllTheWay	= Game_gameplay.selectedUnit.MovementSet.Movement[Game_gameplay.selectedUnitMovePosition].mustMoveAllTheWay;
		var spaces 	  			= Game_gameplay.selectedUnit.MovementSet.Movement[Game_gameplay.selectedUnitMovePosition].spaces;
		var xDirection 			=   parseInt( Math.round( Math.sin( direction * (Math.PI / 180) ) ) );
		var yDirection 			= - parseInt( Math.round( Math.cos( direction * (Math.PI / 180) ) ) );
		
		
		//Figure out the tiles to light up
		//If we have to move all the way we only have one tile to light up
		if( mustMoveAllTheWay ){
			
			finalX	+= ( spaces * xDirection );
			finalY	+= ( spaces * yDirection );
		
		console.log( finalX );
			jQuery( '.gameTile[x="'+finalX+'"][y="'+finalY+'"]' ).addClass( 'highlightedForMove' );
			jQuery( '.gameTile[x="'+finalX+'"][y="'+finalY+'"]' ).attr( 'movementSet', Game_gameplay.currentMovementSet );

		//If it's not a must move all the way move then we light up every tile
		//along the way
		}else{
			
			//Loop through the spaces
			for( spaceCounter = 1; spaceCounter <= spaces; spaceCounter++ ){
			
				finalX = parseInt(Game_gameplay.selectedUnit.x) + ( spaceCounter * xDirection );
				finalY = parseInt(Game_gameplay.selectedUnit.y) + ( spaceCounter * yDirection );
				
		console.log( finalX );
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
		
		//Grab the moved to tile and perform the move
		var tileMovedTo = triggeredEvent.target;
		Game_gameplay.executeMove( tileMovedTo );
				
		//Reset the highlighted units 
		Game_gameplay.unhighlightUnitPaths();
		
		//Now we want to check if the unit has more moves left
		if( Game_gameplay.selectedUnit.movements[0].length > Game_gameplay.selectedUnitMovePosition ){
			Game_gameplay.prepareSelectedUnitForNextMove();
		}
		
	}
	
	//PUBLIC FUNCTION: prepareSelectedUnitForNextMove
	//Prepare the currently selected u nit for the next move it can make
	this.prepareSelectedUnitForNextMove = function(){
		
		Game_gameplay.highlightSelectedUnitPaths();
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
				gameUnitUID:	Game_gameplay.selectedUnitUID,
				x:				nuX,
				y:				nuY
			},
			function( jSONData ){
				alert( 'Valid Move' );
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
	
		//Reset the selected unit turn position and angle
		Game_gameplay.selectedUnitMovePosition  = 0;
		Game_gameplay.angleOfLastMove			= 0;
		
		//Reset the highlighted units 
		jQuery( '.highlightedForMove' ).removeClass( 'highlightedForMove' );
		
	}
	
	//PUBLIC FUNCTION: selectUnit
	//Select the given unit
	this.selectUnit = function( triggeredEvent ){
	
		//Click unit
		var clickedUnit = jQuery( triggeredEvent.target ).closest( '.gameplayUnit' );
		
		//Grab the UID from the unitElement 
		Game_gameplay.selectedUnitUID = jQuery( clickedUnit ).attr( 'uid' );
		
		//Loop through the player's units and find the selected unit
		jQuery.each( window.gameUnits, Game_gameplay.checkIfSelected );
		
		//Reset turn data
		Game_gameplay.resetTurnData();
		
		//Toggle the highlighted and selected units
		Game_gameplay.highlightSelectedUnitPaths();
		Game_gameplay.toggleHighlight( clickedUnit );
		Game_gameplay.toggleSelect( clickedUnit );
		Game_gameplay.handleMoveToTile();
		
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