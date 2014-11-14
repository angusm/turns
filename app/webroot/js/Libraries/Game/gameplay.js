// JavaScript Document

//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all
var Game_elements = null;

//DOCUMENT READY
//When the document is fully ready, call the main functions
require(
	[
		window.Paths.jsDir+'Game/elements.js'
	],
	function(){
		Game_elements = new GameElements();
		Game_gameplay = new Gameplay();
		Game_gameplay.handleEverything();
	}
);

//Alright let's do this matchmaking stuff
var Gameplay = function(){


	//VARIABLES
	this.cardUnitUID    = null;
    this.boardReady     = false;
    this.unitWidth      = 10;
    this.occupiedSpaces = [];

	//PUBLIC FUNCTION: arrangeUnit
	//Arrange the unit
	this.arrangeUnit = function( unitObject ){

        var xPos = parseInt( unitObject.x );
        var yPos = parseInt( unitObject.y );
        var occupiedOffset = 0;

        //Check if a unit already occupies the given space
        if( xPos in Game_gameplay.occupiedSpaces && yPos in Game_gameplay.occupiedSpaces[xPos] ){
            occupiedOffset = Game_gameplay.unitWidth * Game_gameplay.occupiedSpaces[xPos][yPos] / 10
        }

        var nuX = (xPos * Game_elements.tileWidth  / 2 ) - (yPos * Game_elements.tileWidth  / 2 ) + ((window.pageData.Game.Board.width - 1) / 2 * Game_elements.tileWidth ) + ( Game_gameplay.unitWidth / 4) + occupiedOffset;
        var nuY = (yPos * Game_elements.tileHeight / 2 ) + (xPos * Game_elements.tileHeight / 2 ) - ( Game_gameplay.unitWidth / 4) + occupiedOffset;

		jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).animate(
																{
																	'position'	: 'absolute',
																	'left'		: nuX + '%',
																	'top'		: nuY + '%'
																},
																1000
															);

        if( 'undefined' == typeof Game_gameplay.occupiedSpaces[xPos] ){
            Game_gameplay.occupiedSpaces[xPos] = [];
        }
        Game_gameplay.occupiedSpaces[xPos][yPos] = 1;
		
	};

	//PUBLIC FUNCTION: grabSelected
	//Store the given selected unit
	this.grabSelected = function(){



		//Grab the selected unit
        if( 'undefined' !== typeof window.pageData.Game.selected_unit_uid &&
            'undefined' !== typeof window.pageData.Game.GameUnit[window.pageData.Game.selected_unit_uid] ){
            Game_gameplay.selectedUnit = window.pageData.Game.GameUnit[window.pageData.Game.selected_unit_uid];
        }

	};
	
	//PUBLIC FUNCTION: clearEverything
	//Clear everything from the previous turn
	this.clearEverything = function(){
		
		//Reset the highlighted units 
		jQuery( '.highlightedForMove' ).removeClass( 'highlightedForMove' );
		
		//Clear the selected unit DOM's class		
		Game_gameplay.clearSelectedUnit();
		Game_gameplay.unhighlightUnitPaths();
		Game_gameplay.unhighlightEverything();
				
	};
		
	//PUBLIC FUNCTION: clearSelectedUnit
	//Clear the selection of the given unit
	this.clearSelectedUnit = function(){
		
		//Unselect other units and select them
		jQuery( '.selected' ).removeClass( 'selected' );
		jQuery( 'highlighted' ).removeClass( 'highlighted' );
		
	};

    //PUBLIC FUNCTION: createUnitDOMElement
    //Create the unit DOM element
    this.createUnitDOMElement = function( unitObject ){

        //Don't do anything for dead units
        if( 0 >= unitObject.defense ){
            return;
        }

        //See if we can grab the element
        var elementExists = false;
        jQuery( '.gameplayUnit[uid="'+unitObject.uid+'"]').each( function(){
           elementExists = true;
        });

        //If the element doesn't exist then create it
        if( false === elementExists ){

	        //Grab the icons
	        var boardIcon = '';

            //Create the div
            jQuery.each( unitObject.UnitArtSet.UnitArtSetIcon, function( iconPos, unitArtSetIcon ){

                var iconPosition    = unitArtSetIcon.Icon.icon_positions_uid;
                switch( iconPosition ){

                    case "3":
	                    boardIcon = unitArtSetIcon.Icon.image;
		                break;

	                default:
		                //Do nothing
		                break;

                }

            });

            var team = 'user';
            if( unitObject.users_uid != window.pageData.User.uid ){
                team = 'enemy';
            }

            var gameplayUnitDiv =   '<div uid="'+unitObject.uid+'" class="gameplayUnit" team="'+team+'" users_uid="'+unitObject.users_uid+'">' +
                                        '<img src="' + imgLibraryDirectory + boardIcon + '" />' +
                                        '<div class="gameplayUnitAttack">' +
                                            unitObject.damage +
                                        '</div>' +
                                        '<div class="gameplayUnitDefense">' +
                                            unitObject.defense +
                                        '</div>' +
                                    '</div>';

            //Place the unit on the board
            jQuery( 'div.gameBoard' ).append( gameplayUnitDiv );

        }

    };
	
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
		
		
	};
	
	//PUBLIC FUNCTION: displayUnitCard
	//Display the card for a moused over unit
	this.displayUnitCard = function( triggeredEvent ){
	
		//Click unit
		var mousedOverUnit = jQuery( triggeredEvent.target ).closest( '.gameplayUnit' );
		
		//Grab the UID from the unitElement 
		var mousedOverUnitUID = jQuery( mousedOverUnit ).attr( 'uid' );
		
		//Check if this was the last unit we moused over, we don't need to make this call 
		//twice.
		if( mousedOverUnitUID != Game_gameplay.cardUnitUID ){
			
			Game_gameplay.cardUnitUID = mousedOverUnitUID;
			
			//Make the call to the server
			jQuery.getJSON(
				window.Paths.webroot + 'GameUnits/getGameUnitCardInfo',
				{
					uid: mousedOverUnitUID
				},
				function( jSONData ){
					//console.log( jSONData );
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
		
	};
	
	//PUBLIC FUNCTION: endGame
	//End the game
	this.endGame = function(){
		
		alert( 'GAME OVER' );
		
	};
	
	//PUBLIC FUNCTION: executeMove
	//Adjust the stats and display to reflect the move
	this.executeMove = function( tileMovedTo ){
		
		//Grab the new logical X and Y
		var nuLogicalX = jQuery( tileMovedTo ).attr('x');
		var nuLogicalY = jQuery( tileMovedTo ).attr('y');
		
		var movementSetArray = Game_gameplay.selectedUnit.MovementSet;
		
		Game_gameplay.selectedUnit.movements = new Array( movementSetArray );
		
		//Push this move to the server
		Game_gameplay.pushMoveToServer( nuLogicalX, nuLogicalY );
				
		//Change the data of the selected unit
		Game_gameplay.selectedUnit.x = parseInt( nuLogicalX );
		Game_gameplay.selectedUnit.y = parseInt( nuLogicalY );7
		
		//Increase the selected unit move position
		Game_gameplay.selectedUnit.last_movement_priority++;

		//Move the unit visually
		Game_gameplay.setupUnit( 1, Game_gameplay.selectedUnit );
	
	};
	
	//PUBLIC FUNCTION: getGameUpdate
	//Get an update on everything in the game and set things up appropriately
	this.getGameUpdate = function(){

		//Make a request to the server and update all the variables
		jQuery.getJSON(
			window.Paths.webroot + 'Games/getGameUpdate',
			{
				gameUID: 		 window.pageData.Game.uid,
				lastKnownTurn:	 window.pageData.Game.currentTurn
			},
			function( jSONData ){

                switch( jSONData.gameInformation ){

                    case false:
                        Game_gameplay.getGameUpdate();
                        break;

                    case null:
                        //Do nothing
                        break;

                    default:
                        //Process the game update
                        Game_gameplay.processGameUpdate( jSONData );
                        //Check if the game's over
                        if( true == jSONData.gameInformation.game_over ){
                            Game_gameplay.endGame();
                        }
                        break;

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
			
		
	};
	
	//PUBLIC FUNCTION: handleCardDisplay
	//Display the unit card containing all of the unit's statistics when it's moused over
	this.handleCardDisplay = function( unitObject ){
		
		//Add the listener to each unit
		jQuery( '.gameplayUnit[uid="'+unitObject.uid+'"]' ).each( function(){ 
		
			if( ! jQuery(this).isBound( 'mouseover', Game_gameplay.displayUnitCard ) ){
				jQuery(this).bind(
					'mouseover',
					Game_gameplay.displayUnitCard
				)
			}
		
		});
		
	};
	
	//PUBLIC FUNCTION: handleEverything
	//Just be Pepper Potts already, do everything
	this.handleEverything = function(){

        //Trigger the flag that indicates when the board has been setup
        EventBus.addEventListener("GAME_BOARD_CREATED", function(){

            //Get the game data
            Game_gameplay.boardReady = true;

        }, Game_elements );

        //Arrange the board and the pieces
        Game_elements.arrangeStaticElements( window.pageData.Game.uid );

        //Get the game data if we aren't dealing with a demo board
        Game_gameplay.getGameUpdate();

	};
	
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
			jQuery.each( window.pageData.Game.GameUnit, function( unitIndex, unitObject ){
								
				if( parseInt(unitObject.x) == parseInt(highlightedTile.attr('x')) && parseInt(unitObject.y) == parseInt(highlightedTile.attr('y')) ){
					
					//We still only want to add a listener to this unit once
					var unitElement = jQuery('div.gameplayUnit[uid="'+unitObject.uid+'"]');
					
					if( 0 < unitObject.defense && ! unitElement.isBound( 'click', Game_gameplay.moveSelectedUnitToUnit ) ){
						
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
		
	};
	
	//PUBLIC FUNCTION: handleSelectionOfAnyUnit
	//Setup a function to handle the selection of any of the player's units	
	this.handleSelectionOfAnyUnit = function(){
	
		//We only want to be adding this once
		jQuery( '.gameplayUnit[users_uid="'+window.pageData.User.uid+'"]' ).each( function(){
			
			if( ! jQuery(this).isBound( 'click', Game_gameplay.selectUnit ) ){
				jQuery(this).bind( 
					'click',
					Game_gameplay.selectUnit
				);
			}
			
		});
		
	};
	
	//PUBLIC FUNCTION: handleUnitSelection
	//Handle selecting the unit when its clicked on
	this.handleUnitSelection = function(){
		
		//If we still don't have a selected unit then allow the user to select any of their units
		if( false != Game_gameplay.selectedUnit.uid ){
			Game_gameplay.processUnitSelection();	
		}else{
			Game_gameplay.handleSelectionOfAnyUnit();
		}
	
	};
	
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
		
	};
	
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
		
	};
	
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
	
			
		
	};
	
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
				
				
	};
	
	//PUBLIC FUNCTION: moveSelectedUnitToUnit
	//Move the selected unit to the position of the unselected unit
	this.moveSelectedUnitToUnit = function( triggeredEvent ){
			
		//Grab the x and y of the game unit that was the target of this event
		var unitElement = jQuery( triggeredEvent.target ).closest( '.gameplayUnit' );
		
		jQuery.each( window.pageData.Game.GameUnit, function( unitIndex, unitObject ){
			
			if( jQuery(unitElement).attr('uid') == unitObject.uid ){
				
				//Grab the targeted X and Y
				var targetedX = unitObject.x;
				var targetedY = unitObject.y;
				
				//Trigger the event on the tile the targeted unit sits on
				jQuery( 'div.gameTile[x="'+targetedX+'"][y="'+targetedY+'"]' ).click();
				
			}
			
		});
		
	};
	
	//PUBLIC FUNCTION: processGameUpdate
	//Handle game update
	this.processGameUpdate = function( jSONData ){
		
		//Set the new turn 
		window.pageData.Game.currentTurn        = jSONData.gameInformation.Game.turn;
										
		//Grab the game update, if we have no previous update then we can just do a direct pass
        if( window.pageData.Game.GameUnit == undefined ){

            window.pageData.Game.GameUnit = jSONData.gameInformation.GameUnit;

        //If we already have all the static information we only want to update the dynamic data
        }else{
            jQuery.each( jSONData.gameInformation.GameUnit, function( gameUnitIndex, gameUnitData ){
                jQuery.each( gameUnitData, function( newKey, newValue ){
                    if( gameUnitIndex in window.pageData.Game.GameUnit ){
                        window.pageData.Game.GameUnit[gameUnitIndex][newKey] = newValue;
                    }
                });
            });
        }

        //Note the selected unit
		window.pageData.Game.selected_unit_uid = jSONData.gameInformation.Game.selected_unit_uid;
		if( null === window.pageData.Game.selected_unit_uid ){
			Game_gameplay.resetSelectedUnit();
		}

		//Check if it is the current player's turn
		window.pageData.Game.playersTurn = jSONData.gameInformation.ActiveUser[0].UserGame.users_uid == window.pageData['User']['uid'];
		
		//Reset the turn data
		Game_gameplay.resetTurnData();
		
	};
	
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
		
	};
	
	//PUBLIC FUNCTION: pushMoveToServer
	//Push the selected move the unit has made to the server
	//Then if it's successful we'll proceed to finalize the move
	this.pushMoveToServer = function( nuX, nuY ){
		
		//Make the call to the server
		jQuery.getJSON(
			window.Paths.webroot + 'Games/processUnitMove',
			{
				gameUnitUID:	Game_gameplay.selectedUnit.uid,
				x:				nuX,
				y:				nuY
			},
			function(){
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
		
	};
	
	//PUBLIC FUNCTION: resetSelectedUnit
	//Reset the selected unit
	this.resetSelectedUnit = function(){
	
		Game_gameplay.selectedUnit 		= new Object();
		Game_gameplay.selectedUnit.uid 	= false;
		
	};
	
	//PUBLIC FUNCTION: resetTurnData
	//Reset everything as if a unit has never moved
	this.resetTurnData = function(){

        if( Game_gameplay.boardReady ){

	        //Reposition all the units
	        Game_gameplay.setupUnits();

	        //Clear everything
	        Game_gameplay.clearEverything();

	        //Grab the selected unit
	        Game_gameplay.grabSelected();

	        //If its the player's turn then setup unit selection,
	        //otherwise setup a timer to check if it's the user's turn yet
	        if( window.pageData.Game.playersTurn ){
		        //Handle selecting of the player's units
		        Game_gameplay.handleUnitSelection();
	        }else{
		        //Setup callback timer to get game updates
		        Game_gameplay.getGameUpdate();
	        }

        }else{

	        //Don't setup the game until the board is ready
	        EventBus.addEventListener("GAME_BOARD_CREATED", function(){

		        Game_gameplay.boardReady = true;
		        Game_gameplay.resetTurnData();

	        }, Game_elements );

        }

	};
	
	//PUBLIC FUNCTION: selectUnit
	//Select the given unit
	this.selectUnit = function( triggeredEvent ){
	
		//Click unit
		var clickedUnit = jQuery( triggeredEvent.target ).closest( '.gameplayUnit' );
		
		//Grab the UID from the unitElement 
		window.pageData.Game.selected_unit_uid = jQuery( clickedUnit ).attr( 'uid' );
		
		//Loop through the player's units and check if there is a unit with
		//a move already in progress and select that Unit instead.
        Game_gameplay.grabSelected();
		
		Game_gameplay.processUnitSelection();
		
	};
	
	//PUBLIC FUNCTION: setupUnit
	//Setup the given unit
	this.setupUnit = function( unitObjectPosition, unitObject ){

        //Create the DOM element for the unit if it doesn't
        //already exist
        Game_gameplay.createUnitDOMElement( unitObject );

		//Set the unit's defense and damage display
		Game_gameplay.updateUnitStats( unitObject );
		
		//Add the event handler that will display cards on a mouse over
		Game_gameplay.handleCardDisplay( unitObject );

		//If the unit is still alive, move it into position
		if( 0 < unitObject.defense ){
						
			//Align the unit with its tile position
			Game_gameplay.arrangeUnit( unitObject );
		
		//If the unit isn't alive, kill it	
		}else{
			
			//Kill it
			Game_gameplay.killUnit( unitObject );
			
		}
		
	};
	
	//PUBLIC FUNCTION: setupUnits
	//Arrange all of the gameplay units
	this.setupUnits = function(){

        //Reset the occupied spaces
        Game_gameplay.occupiedSpaces = new Array();

		//Loop through all of the units
		jQuery.each( window.pageData.Game.GameUnit,  Game_gameplay.setupUnit );

	};
	
	//PUBLIC FUNCTION: unhighlightEverything
	//Unhighlight everything that has been highlighted
	this.unhighlightEverything = function(){
	
		jQuery( '.highlighted' ).removeClass( 'highlighted' );	
		jQuery( '.highlightedForMove' ).removeClass( 'highlightedForMove' );
		
	};
	
	//PUBLIC FUNCTION: unhighlightUnitPaths
	//Remove all the paths for the selected unit
	this.unhighlightUnitPaths = function(){

        var gameplayUnitDOMElements         = jQuery('.gameplayUnit');
        var highlightedForMoveDOMElements   = jQuery('.highlightedForMove');

        //Do nothing if there's nothing to do anything to
        if( 0 != gameplayUnitDOMElements.length ){
            //Remove the event listeners from any units
            jQuery.each( gameplayUnitDOMElements, function(){

                if( jQuery(this).isBound( 'click', Game_gameplay.moveSelectedUnitToUnit ) ){
                        jQuery(this).unbind(
                        'click',
                        Game_gameplay.moveSelectedUnitToUnit
                    );
                }

            });
        }

        //Do nothing if there's nothing to do anything to
        if( 0 != highlightedForMoveDOMElements.length ){
            //Remove the event listener for the highlighted tiles
            jQuery.each( highlightedForMoveDOMElements, function(){

                if( jQuery(this).isBound( 'click', Game_gameplay.moveSelectedUnitToTile ) ){
                    jQuery(this).unbind(
                        'click',
                        Game_gameplay.moveSelectedUnitToTile
                    );
                }
                //Remove the highlighted class
                jQuery(this).removeClass('highlightedForMove');

            });

        }
		
	};
	
	//PUBLIC FUNCTION: updateUnitStats
	//Update the unit's statistics as they are displayed on its game tile
	this.updateUnitStats = function( unitObject ){
		
		//For this unit set its damage and defense
		jQuery( '.gameplayUnit[uid="'+unitObject.uid+'"] > .gameplayUnitAttack' ).html( unitObject.damage );
		jQuery( '.gameplayUnit[uid="'+unitObject.uid+'"] > .gameplayUnitDefense' ).html( unitObject.defense );
		
	}
	
};