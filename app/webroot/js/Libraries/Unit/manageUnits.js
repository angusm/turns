//PEPPER POTTS FUNCTION: handleEverything



//Setup the dependencies function
var loadDependenciesFor_Unit_manageUnits = function(){

    libraries.push( ['Game', 'elements'] );
    libraries.push( ['Game', 'gameplay'] );
    libraries.push( ['Card', 'CardManager'] );

};

//DOCUMENT READY
//When the document is fully ready, call the main function
jQuery(document).ready( function(){


    //Initialize the game elements and gameplay
    Game_elements = new GameElements();
    Game_gameplay = new Gameplay();

    //Setup the card manager
    Card_CardManager = new CardManager();

    //Setup the unit manager
    Unit_manageUnits = new ManageUnits();
    Unit_manageUnits.handleEverything();

});

//Setup the object for managing units
function ManageUnits(){

    //Track whether or not the board is ready
    this.boardReady = false;

	//Establish a variable to hold the UID of the currently
	//selected unit type UID
	this.selectedUnitTypeUID = false;
	
	//PUBLIC FUNCTION: addNewTeam
	//Allow the user to add a new team to their account
	this.addNewTeam = function( triggeringEvent ){

		//Run the JSON to create the new team
		jQuery.getJSON(
			homeURL + '/Teams/addNewTeam/', 
			{
			},
			function( jSONData ){
				//Finish up the team add
				Unit_manageUnits.finalizeTeamAdd( jSONData );
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
	
	//PUBLIC FUNCTION: addUnitToTeam
	//Add the unit to the selected team
	this.addUnitToTeam = function( tileElement ){

        //Grab the x and y of the selected tile
        tileElement     = jQuery( tileElement ).closest( 'div.gameTile' );
        var selectedX   = jQuery( tileElement ).attr( 'x' );
        var selectedY   = jQuery( tileElement ).attr( 'y' );

        //See if there's already a unit positioned on the given tile
        if( 0 != jQuery('div.removeFromTeam[x="' + selectedX + '"][y="' + selectedY + '"]').length ){
			return false;
		}

		//Grab the Team UID 
		var teamUID 	= jQuery( '.editableSelect[modelname="Team"]' ).val();
		
		//Make the necessary call
		jQuery.getJSON(
			homeURL + '/TeamUnits/addUnitToTeamByUnitTypeUID/', 
			{
				teamUID:		teamUID,
				unitTypeUID:	Unit_manageUnits.selectedUnitTypeUID,
				x:				selectedX,
				y:				selectedY
			},
			function( jSONData ){
				Unit_manageUnits.finalizeUnitAdd( jSONData );
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
	
	//PUBLIC FUNCTION: debitUnitPool
	//Subtract the unit counts of the team list from the player's unit pool
	this.debitUnitPool = function(){
		
		//Loop through the jSONData returned to grab each unit
		jQuery.each( window.pageData.TeamUnits, function( key, unitData ){
			
			//Grab the relevant data
			var unitUID		= unitData['UnitType']['uid'];
			var unitCount 	= unitData['TeamUnit']['quantity'];
						
			//Now we grab the count for the relevant element in the unit pool
			var poolCount	= jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitUID+'"]' ).attr("value");
			
			//Finally we calculate the debit count
			var debitedCount = parseInt( poolCount ) - parseInt( unitCount );
			
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitUID+'"]' ).attr( "value", debitedCount );
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitUID+'"]' ).html( debitedCount );
						
		});
			
	};
	
	//PUBLIC FUNCTION: displayTeamUnits
	//Display the starting positions of the units on the team
	this.displayTeamUnits = function(){

        //Loop through the jSONData returned to grab each unit
        jQuery.each( window.pageData.TeamUnits, function( key, unitData ){

            //Grab the unit's attack and defense
            var unitAttack  = unitData['UnitType']['UnitStat']['damage'];
            var unitDefense = unitData['UnitType']['UnitStat']['defense'];
            var unitBoardIcon   = 'CardArt/Default/boardIcon.png';

            //Grab the unit's board icon
            jQuery.each( unitData['UnitType']['UnitArtSet']['UnitArtSetIcon'], function( iconKey, iconData ){
                if( 3 == iconData['Icon']['icon_positions_uid'] ){
                    unitBoardIcon = iconData['Icon']['image'];
                    return false;
                }
            });

            jQuery.each( unitData['TeamUnitPosition'], function( key, teamUnitPositionData ){

				//Grab the x, y and team units UID
				var x 			= teamUnitPositionData.x;
				var y 			= teamUnitPositionData.y;
                var uid         = teamUnitPositionData.uid;

                //Display the unit
                jQuery( 'div.gameBoard' ).append(
                    '<div uid="'+uid+'" class="gameplayUnit" team="user" unitTypeUID="'+unitData['UnitType']['uid']+'">'
                        + '<img src="'+ imgURL + unitBoardIcon +'" >'
                        + '<div class="gameplayUnitAttack">'+unitAttack+'</div>'
                        + '<div class="gameplayUnitDefense">'+unitDefense+'</div>'
                        + '<div class="removeFromTeam" uid="'+unitData['UnitType']['uid']+'" x="'+x+'" y="'+y+'">'
                        + '</div>'
                );

                var xPos = x;
                var yPos = y;
                var occupiedOffset = 0;

                var nuX = (xPos * Game_elements.tileWidth  / 2 ) - (yPos * Game_elements.tileWidth  / 2 ) + ((window.pageData.Game.Board.width - 1) / 2 * Game_elements.tileWidth ) + ( Game_gameplay.unitWidth / 4) + occupiedOffset;
                var nuY = (yPos * Game_elements.tileHeight / 2 ) + (xPos * Game_elements.tileHeight / 2 ) - ( Game_gameplay.unitWidth / 4) + occupiedOffset;

                jQuery( '.gameplayUnit[uid="'+uid+'"]' ).animate(
                    {
                        'position'	: 'absolute',
                        'left'		: nuX + '%',
                        'top'		: nuY + '%'
                    },
                    1000
                );
				
			});
			
		});

        Unit_manageUnits.handleRemoveFromTeamButton();
        Unit_manageUnits.handleMouseoverUnit();
		
	};

    //PUBLIC FUNCTION: displayUnitCard
    //Handle the display of a unit
    this.displayUnitCard = function( gameplayUnitElement ){

        var unitTypeUID = jQuery( gameplayUnitElement).attr('unitTypeUID');
        Card_CardManager.showCardByUnitType( unitTypeUID );

    };


	//PUBLIC FUNCTION: finalizeTeamAdd
	//Handle the callback function after we've added the team to the database
	//So that we can show this addition to the user
	this.finalizeTeamAdd = function( jSONData ){
		
		//Make sure we're not dealing with an unsuccessful add
		if( false != jSONData['teamData'] ){
			
			//Grab the team data
			var teamData = jSONData['teamData']['Team'];
					
			//Create the new option
			jQuery( 'select[modelName="Team"].editableSelect' ).append(
				'<option ' +
					'uid="'			+ teamData['uid'] 		+ '" ' +
					'name="'		+ teamData['name'] 		+ '" ' +
					'users_uid="'	+ teamData['users_uid'] + '" ' +
					'value="' 		+ teamData['uid'] 		+ '"'  +
					'>' + teamData['name'] + '</option>'
			);
		
			//Select the new option
			jQuery( 'select[modelName="Team"].editableSelect' ).val( teamData['uid'] );
		
			//Adjust the text box
			jQuery( 'select[modelName="Team"].editableSelect' ).trigger( 'change' );
		
			Unit_manageUnits.loadTeamUnits();
		
		}
		
	};
	
	//PUBLIC FUNCTION: finalizeUnitAdd
	//Once we've gotten confirmation from the server we can actually show the add as completed to the user
	this.finalizeUnitAdd = function( jSONData ){
		
		//If the add was successful then update the display
		if( false != jSONData['success'] ){
            Unit_manageUnits.loadTeamUnits();
		}
		
	};
	
	//PUBLIC FUNCTION: finalizeUnitRemove
	//Once we've gotten confirmation from the server we can actually show the remove as completed to the user
	this.finalizeUnitRemove = function( jSONData ){
		
		//If the remove was successful then update the display
		if( false != jSONData['success'] ){
            Unit_manageUnits.loadTeamUnits();
		}
		
	};
	
	//PUBLIC FUNCTION: getUnitTDCell
	//Create a cell with some unit data in there
	this.getUnitTDCell = function( uid, name, value ){
		
		return  '<td modelName="Unit" ' +
				'uid="' 		+ uid 	+ '" ' +
				'fieldName="' 	+ name 	+ '" ' +
				'value="'		+ value + '" ' +
				'>' 			+ value + '</td>';
				
		
	};
	
	//PUBLIC FUNCTION: handleAddToTeamButton
	//Handles someone clicking on the button to add the given unit to their
	//team
	this.handleAddToTeamButton = function(){
	
		//Highlight the starting positions this unit can be placed on
	
		//Throw the listener on
		jQuery( '.addUnitToTeamButton' ).click( function(){
			Unit_manageUnits.highlightSelectedUnit( this );
		});
		
	};
	
	//PUBLIC FUNCTION: handleChangeTeam
	//If the user changes the selected team then we better change
	this.handleChangeTeam = function(){
	
		//Change the units displayed in the team pool and adjust what's shown in the unit pool
		jQuery( '.editableSelect[modelname="Team"]' ).each( function(){
			
			if( ! jQuery(this).isBound( 'change', Unit_manageUnits.loadTeamUnits ) ){
				jQuery(this).bind( 'change',
					Unit_manageUnits.loadTeamUnits
				);
			}
			
		});
		
	};
	
	//PUBLIC FUCNTION: handleChangeTeamName
	//Save team name changes to the database
	this.handleChangeTeamName = function(){
		
		//Add a handler to the <input>s
		jQuery( 'input[modelName="Team"][type="button"].editableSelectSave' ).each( function(){
			
			if( ! jQuery(this).isBound( 'click', Unit_manageUnits.saveTeamName ) ){
				jQuery(this).click(
					Unit_manageUnits.saveTeamName
				);
			}
			
		});
				
	};
	
	//PUBLIC FUNCTION: handleEverything
	//The Pepper Potts function, in that it will just handle everything
	this.handleEverything = function(){

        //Load the card data
        Unit_manageUnits.loadCardData();

        //Place the units
        if( true === Unit_manageUnits.boardReady ) {
	        Unit_manageUnits.handlePlaceUnit();
        }else{

            EventBus.addEventListener("GAME_BOARD_CREATED", function(){

                //Get the game data
                Unit_manageUnits.boardReady = true;
                Unit_manageUnits.handlePlaceUnit();

            }, Game_elements );

        }

		Unit_manageUnits.handleAddToTeamButton();
		Unit_manageUnits.handleChangeTeam();
		Unit_manageUnits.handleChangeTeamName();
		Unit_manageUnits.handleNewTeamButton();
		Unit_manageUnits.handleRemoveTeam();
		Unit_manageUnits.loadTeamUnits();

	};

    //PUBLIC FUNCTION: handleMouseoverUnit
    //Handle displaying unit information in a card during a mouseover
    this.handleMouseoverUnit = function(){

        //Remove previous handlers so we're starting fresh
        jQuery('div.gameplayUnit').each( function(){

            if( ! jQuery(this).isBound( 'mouseover', Unit_manageUnits.displayUnitCard ) ){
                jQuery(this).mouseover( function(){
                    Unit_manageUnits.displayUnitCard( this );
                });
            }

        });

    };
	
	//PUBLIC FUNCTION: handleNewTeamButton
	//Handle the button that allows the user to create a new team
	this.handleNewTeamButton = function(){

		//Add our sick new handler
		jQuery( 'input[modelName="Team"][type="button"].editableSelectNew' ).each( function(){

            //Remove the default handler
            jQuery( this ).unbind( 'click' );

			if( ! jQuery(this).isBound( 'click', Unit_manageUnits.addNewTeam ) ){
				jQuery(this).click(
					Unit_manageUnits.addNewTeam
				);
			}
			
		});
		
	};
	
	//PUBLIC FUNCTION: handlePlaceUnit
	//Handle placing the selected unit on a clicked tile
	this.handlePlaceUnit = function(){
		
		//Add our sick new handler
		jQuery( '.gameTile' ).each( function(){
			
			if( ! jQuery(this).isBound( 'click', Unit_manageUnits.placeSelectedUnit ) ){
				jQuery(this).click(
					Unit_manageUnits.placeSelectedUnit
				);
			}
			
		});
		
	};
	
	//PUBLIC FUNCTION: handleRemoveFromTeamButton
	//Handles someone clicking on the button to remove the given unit from
	//their team
	this.handleRemoveFromTeamButton = function(){
		
		//Don't add duplicates
		jQuery('.removeFromTeam').each( function(){
			if( ! jQuery( this ).isBound( 'click', Unit_manageUnits.removeUnitFromTeam ) ){
				//Throw the listener on
				jQuery( this ).click(
					Unit_manageUnits.removeUnitFromTeam
				);
			}
		});
		
	};
	
	//PUBLIC FUNCTION: handleRemoveTeam
	//Remove the selected team from the database when it is clicked
	this.handleRemoveTeam = function(){
		
		//Don't add duplicates
		if( ! jQuery('.editableSelectRemove').isBound( 'click', Unit_manageUnits.removeTeam ) ){
			//Throw the listener on
	
			jQuery('.editableSelectRemove').unbind( 'click', EditableSelect_editableSelect.removeOption );
			jQuery( '.editableSelectRemove' ).click(
				Unit_manageUnits.removeTeam
			);
		}
		
	};
	
	//PUBLIC FUNCTION: highlightSelectedUnit
	//Highlight the row of the unit that was selected to be added
	//Ideally this will eventually incorporate the display of the unit
	//card and all that other fun shit.
	this.highlightSelectedUnit = function( selectedUnit ){
		
		//Grab the selected unit type UID
		Unit_manageUnits.selectedUnitTypeUID = jQuery( selectedUnit ).attr('uid');
		
		//Highlight anything to do with that unit
		jQuery( '[modelName="Unit"][uid="' + Unit_manageUnits.selectedUnitTypeUID + '"]' ).addClass( 'Unit_manageUnits_selectedUnit' );
		
	};

    //PUBLIC FUNCTION: loadCardData
    //Load all the card data for the units the player has available
    this.loadCardData = function(){

        jQuery( 'div.unitPool').find('tr[modelname="Unit"][uid]').each( function(){
            Card_CardManager.loadCardData( jQuery(this).attr('uid') );
        });

    };
	
	//PUBLIC FUNCTION: loadTeamUnits
	//Load all the units on a team into the display box on the page
	this.loadTeamUnits = function(){
		
		//Get the controller name so that we're creating the right type 
		//of new record
		var teamUID = jQuery( 'select.editableSelect[modelname="Team"]' ).val();
		
		//Make the necessary call
		jQuery.getJSON(
			homeURL + '/TeamUnits/getUnitsOnTeam/', 
			{
				teamUID:teamUID
			},
			function( jSONData ){

                window.pageData.TeamUnits = jSONData['unitsOnTeam'];

                if( Unit_manageUnits.boardReady ) {
	                Unit_manageUnits.processTeamLoad();
                }else{

                    //Don't setup the game until the board is ready
                    EventBus.addEventListener("GAME_BOARD_CREATED", function(){

                        Game_gameplay.boardReady = true;
                        Unit_manageUnits.processTeamLoad();

                    }, Game_elements );

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
	
	//PUBLIC FUNCTION: placeSelectedUnit
	//Places the currently selected unit into the given position
	//on the given team.
	this.placeSelectedUnit = function( triggeringEvent ){
		
		//Make sure we have a selected unit, if we do then continue,
		//otherwise do nothing
		if( false != Unit_manageUnits.selectedUnitTypeUID ){

			//Add the selected unit, passing the selected tile as a parameter
			Unit_manageUnits.addUnitToTeam( triggeringEvent.target );
			
		}
		
	};
	
	//PUBLIC FUNCTION: populateTeamUnits
	//Build the HTML that will actually display the team units
	this.populateTeamUnits = function(){
		
		//Team Cost 
		var teamCost = 0;
		
		//Loop through the jSONData returned to grab each unit
		jQuery.each( window.pageData.TeamUnits, function( key, unitData ){
			
			//Grab the relevant data
			var unitCount 	= unitData['TeamUnit']['quantity'];
			var unitName 	= unitData['UnitType']['name'];
			var unitUID		= unitData['UnitType']['uid'];
			var unitTeamCost = jQuery( 'td[modelname="Unit"][fieldname="teamcost"][uid="'+unitUID+'"]' ).attr( 'value' );
			//Add the amount to the team cost
			teamCost += unitCount * unitTeamCost;
			
			var unitRow 	=  '<tr modelName="Unit" uid="' + unitUID + '">';
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'uid', 		unitUID );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'name', 	unitName );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'quantity', unitCount );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'teamcost', unitTeamCost );
			unitRow			+= '</tr>';
			
			//Create the necessary element
			jQuery( 'div.teamUnits > table > tbody' ).append( unitRow );
			Unit_manageUnits.handleRemoveFromTeamButton();
						
		});
		
		//Display the Team Cost
		jQuery( 'div.TeamCost' ).html( teamCost );
		
		//Debit the unitl pool
		Unit_manageUnits.debitUnitPool();
		
	};

    //PUBLIC FUNCTION: processTeamLoad
    //When we have team data and the board is ready, show it all to the user
    this.processTeamLoad = function(){
        Unit_manageUnits.refundUnitPool();
        Unit_manageUnits.populateTeamUnits();
        Unit_manageUnits.displayTeamUnits();
    };
	
	//PUBLIC FUNCTION: refundUnitPool
	//Take all the Unit Types that are in the currently selected team and refund their
	//quantities to the unit pool
	this.refundUnitPool = function(){
		
		//We refund for each element
		jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="quantity"]' ).each( function(){
			
			//Get the count and unit type UID
			var teamCount	= jQuery(this).attr("value");
			var unitTypeUID = jQuery(this).attr("uid");
			
			//Now we grab the count for the relevant element in the unit pool
			var poolCount	= jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value");
			
			//Calculate and set the new pool count
			var refundedCount	= parseInt(poolCount) + parseInt(teamCount);
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr( "value", refundedCount );
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).html( refundedCount );			
			
		});
		
		//Now with everything refunded we remove the table rows and displayed units
		jQuery( 'div.teamUnits > table > tbody > tr[modelName="Unit"]' ).remove();
        jQuery( 'div.gameplayUnit').remove();
		
	};
	
	//PUBLIC FUNCTION: removeTeam
	//Remove the team from the database
	this.removeTeam = function( triggeredEvent ){
		
		//Grab the team UID
		var teamUID 	= jQuery( '.editableSelect[modelname="Team"]' ).val();
		
		//Remove the team from the select
		EditableSelect_editableSelect.removeOption( triggeredEvent );
		Unit_manageUnits.loadTeamUnits();
		
		//Make the call
		jQuery.getJSON(
			homeURL + '/Teams/remove/',
			{
				uid:teamUID
			},
			function( jSONData ){
			}
		).done(
			function(data){
			}
		).fail(
			function(data){
			}
		).always(
			function(data){
			}
		);
		
	};
	
	//PUBLIC FUNCTION: removeUnitFromTeam
	//Remove the unit from the selected team
	this.removeUnitFromTeam = function( triggeredEvent ){
	
		var element = triggeredEvent.target;
	
		//Grab the unit type UID and the team UID so we can make a call to the servers
		var teamUID 	= jQuery( '.editableSelect[modelname="Team"]' ).val();
		var unitTypeUID = jQuery( element ).attr('uid');
		var x 			= jQuery( element ).attr('x');
		var y 			= jQuery( element ).attr('y');
		
		//Make the necessary call
		jQuery.getJSON(
			homeURL + '/TeamUnits/removeUnitFromTeamByUnitTypeUID/', 
			{
				teamUID:		teamUID,
				unitTypeUID:	unitTypeUID,
				x:				x,
				y:				y
			},
			function( jSONData ){
				Unit_manageUnits.finalizeUnitRemove( jSONData );
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
	
	//PUBLIC FUNCTION: saveTeamName
	//Save the team name to the database
	this.saveTeamName = function( triggeredEvent ){
		
		//Get the button that was clicked
		var element = triggeredEvent.target;
		
		//Grab the editableSelect value
		var editableSelectUID = jQuery( element ).attr( 'editableSelect' );
		
		//Now grab the value stored in the corresponding text box
		var typedText 	= jQuery( 'input[type="text"][editableSelect="' + editableSelectUID + '"].editableSelect' ).val();
		
		//We'll also need to grab the team UID
		var teamUID		= jQuery( 'select[editableSelect="' + editableSelectUID + '"].editableSelect > option:selected' ).attr( 'uid' );
		
		//Make the necessary call
		jQuery.getJSON(
			homeURL + '/Teams/changeTeamName/', 
			{
				teamUID:	teamUID,
				teamName:	typedText
			},
			function( jSONData ){
				//Do nothing
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
	
}