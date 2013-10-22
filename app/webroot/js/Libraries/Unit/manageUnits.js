//PEPPER POTTS FUNCTION: handleEverything

var Unit_manageUnits = new manageUnits();
Unit_manageUnits.handleEverything();

//Setup the object for managing units
function manageUnits(){
	
	//Establish a variable to hold the UID of the currently
	//selected unit type UID
	this.selectedUnitTypeUID = false;
	
	//PUBLIC FUNCTION: addNewTeam
	//Allow the user to add a new team to their account
	this.addNewTeam = function( triggeringEvent ){
		
		//Get the targeted element
		var element = triggeringEvent.target;	
		
		//Get the editableSelect value
		var editableSelectUID = jQuery( element ).attr( 'editableSelect' );
		
		//Run the JSON to create the new team
		
		//Make the necessary call
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
		
	}
	
	//PUBLIC FUNCTION: addTeamRowForUnitTypeUID
	//Add a row to the team table for the given UnitTypeUID
	this.addTeamRowForUnitTypeUID = function( unitTypeUID ){
	
			
			//Grab the relevant data
			var unitName 	= jQuery( 'td[fieldName="name"][uid="'+unitTypeUID+'"]' ).html();
			var teamCost 	= jQuery( 'td[fieldName="teamcost"][uid="'+unitTypeUID+'"]' ).html();
			var unitRow 	=  '<tr modelName="Unit" uid="' + unitTypeUID + '">';
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'uid', 		unitTypeUID );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'name', 	unitName );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'quantity', 0 );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'teamcost', teamCost );
			unitRow			+= '</tr>';
			
			//Create the necessary element
			jQuery( 'div.teamUnits > table > tbody' ).append( unitRow );
			Unit_manageUnits.handleRemoveFromTeamButton();
		
		
	}
	
	//PUBLIC FUNCTION: addUnitToTeam
	//Add the unit to the selected team
	this.addUnitToTeam = function( tileElement ){
		
		if( jQuery( tileElement ).hasClass('removeFromTeam') ){
			return false;
		}
		
		tileElement = jQuery( tileElement ).closest( 'div.gameTile' );
	
		//Grab the x and y of the selected tile
		var selectedX = jQuery( tileElement ).attr( 'x' );
		var selectedY = jQuery( tileElement ).attr( 'y' );
		console.log( selectedX );
		console.log( selectedY );
		
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
				Unit_manageUnits.finalizeUnitAdd( jSONData, selectedX, selectedY );
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
	
	//PUBLIC FUNCTION: debitUnitPool
	//Subtract the unit counts of the team list from the player's unit pool
	this.debitUnitPool = function( jSONData ){
		
		//Loop through the jSONData returned to grab each unit
		jQuery.each( jSONData['unitsOnTeam'], function( key, unitData ){
			
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
			
	}
	
	//PUBLIC FUNCTION: displayTeamUnits
	//Display the starting positions of the units on the team
	this.displayTeamUnits = function( jSONData ){
	
		//Before we display team units we need to clear out any old ones
		jQuery( '.gameTile' ).html( '' );
	
		//Loop through each team unit, grab its image and display it for each
		//time it has been placed
		jQuery.each( jSONData['unitsOnTeam'], function( 
												teamUnitKey,
												teamUnitData ){
			
			//Grab the unit type uid and the board icon
			var unitTypeUID 	= teamUnitData['TeamUnit']['unit_types_uid'];
			var unitBoardIcon 	= ''
			jQuery.each( window.Unit_manageUnits_availableUnitList, function( key, unitData ){
				
				//Check for a match
				if( unitData['UnitType']['uid'] == unitTypeUID ){
								
					//If we have a valid unit then grab its icon
					jQuery.each( unitData['UnitType']['UnitArtSet']['UnitArtSetIcon'], function( iconKey, iconData ){
						if( iconData['Icon']['icon_positions_uid'] == 3 ){
							unitBoardIcon = iconData['Icon']['image'];	
						}						
						return false;
					});
									
					//Break the loop early	
					return false;	
				}
				
			});	
			
			jQuery.each( teamUnitData['TeamUnitPosition'], function( 
													teamUnitPositionKey, 
													teamUnitPositionData ){
				//Grab the x, y and team units UID
				var x 			= teamUnitPositionData.x;
				var y 			= teamUnitPositionData.y;
				
				Unit_manageUnits.displayUnit( x, y, unitBoardIcon, unitTypeUID );
				
			});
			
		});
		
	}
	
	//PUBLIC FUNCTION: displayUnit
	//Display the unit with the given information including any necessary 
	this.displayUnit = function( x, y, image, uid ){
		
		
		jQuery( 'div.gameTile[x="'+x+'"][y="'+y+'"]' ).append( 
			'<img src="' + imgURL + image + '" class="gameplayUnit" uid="' + uid + '">' +
			'<div class="removeFromTeam" uid="' + uid + '" x="' + x + '" y="' + y + '">'
		);
		Unit_manageUnits.handleRemoveFromTeamButton();
		
	}
	
	//PUBLIC FUNCTION: finalizeTeamAdd
	//Handle the callback function after we've added the team to the database
	//So that we can show this addition to the user
	this.finalizeTeamAdd = function( jSONData ){
		
		//Make sure we're not dealing with an unsuccessful add
		if( jSONData['teamData'] != false ){
			
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
		
	}
	
	//PUBLIC FUNCTION: finalizeUnitAdd
	//Once we've gotten confirmation from the server we can actually show the add as completed to the user
	this.finalizeUnitAdd = function( jSONData, x, y ){
		
		//If the add was successful then update the display
		if( jSONData['success'] != false ){
			
			//Grab the unit type UID
			var unitTypeUID = jSONData['unitTypeUID'];
			
			//Update the pool count
			var originalPoolCount = jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedPoolCount = parseInt( originalPoolCount ) - 1;
			
			//Display the Team Cost
			var teamCost = jQuery( 'div.TeamCost' ).html();
			var unitTeamCost = jQuery( 'td[fieldName="teamcost"][uid="'+unitTypeUID+'"]' ).html();
			teamCost = parseInt( teamCost ) + parseInt( unitTeamCost );
			jQuery( 'div.TeamCost' ).html( teamCost );
			
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value", changedPoolCount);
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).html(changedPoolCount);
			
			//Ensure that a row for this unit exists in the team table
			if( jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).length == 0 ){
			
				//Add the given row
				Unit_manageUnits.addTeamRowForUnitTypeUID( unitTypeUID );
			}
			
			//Update the team count
			var originalTeamCount = jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedTeamCount = parseInt( originalTeamCount ) + 1;
			
			jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value", changedTeamCount);
			jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).html(changedTeamCount);
			
			//Display the given unit at the desired position
			//First we have to find the unit in the available unit team list
			//Then we can display its image in the unit display box
			jQuery.each( window.Unit_manageUnits_availableUnitList, function( key, unitData ){
				
				//Check for a match
				if( unitData['UnitType']['uid'] == unitTypeUID ){
								
					//If we have a valid unit then display it
					jQuery.each( unitData['UnitType']['UnitArtSet']['UnitArtSetIcon'], function( iconKey, iconData ){
						if( iconData['Icon']['icon_positions_uid'] == 3 ){
							
							Unit_manageUnits.displayUnit( x, y, iconData['Icon']['image'], unitData['UnitType']['uid'] );
							return false;
						}						
					});
									
					//Break the loop early	
					return false;	
				}
				
			});
			
		}
		
	}
	
	//PUBLIC FUNCTION: finalizeUnitRemove
	//Once we've gotten confirmation from the server we can actually show the remove as completed to the user
	this.finalizeUnitRemove = function( jSONData ){
		
		//If the remove was successful then update the display
		if( jSONData['success'] != false ){
			
			//Grab the unit type UID
			var unitTypeUID = jSONData['unitTypeUID'];
			
			//Update the pool count
			var originalPoolCount = jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedPoolCount = parseInt( originalPoolCount ) + 1;
			
			
			//Display the Team Cost
			var teamCost = jQuery( 'div.TeamCost' ).html();
			var unitTeamCost = jQuery( 'td[fieldName="teamcost"][uid="'+unitTypeUID+'"]' ).html();
			teamCost = parseInt( teamCost ) - parseInt( unitTeamCost );
			jQuery( 'div.TeamCost' ).html( teamCost );
			
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value", changedPoolCount);
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).html(changedPoolCount);
			
			//Update the team count
			var originalTeamCount = jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedTeamCount = parseInt( originalTeamCount ) - 1;
			
			if( changedTeamCount == 0 ){
				jQuery( 'div.teamUnits > table > tbody > tr[uid="'+unitTypeUID+'"]' ).remove();
			}else{
				jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value", changedTeamCount);
				jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).html(changedTeamCount);
			}
			
			var x = jSONData['x'];
			var y = jSONData['y'];
			jQuery( 'div.gameTile[x="' + x + '"][y="' + y + '"]' ).html( '' );
			
		}
		
	}
	
	//PUBLIC FUNCTION: getUnitTDCell
	//Create a cell with some unit data in there
	this.getUnitTDCell = function( uid, name, value ){
		
		return  '<td modelName="Unit" ' +
				'uid="' 		+ uid 	+ '" ' +
				'fieldName="' 	+ name 	+ '" ' +
				'value="'		+ value + '" ' +
				'>' 			+ value + '</td>';
				
		
	}
	
	//PUBLIC FUNCTION: handleAddToTeamButton
	//Handles someone clicking on the button to add the given unit to their
	//team
	this.handleAddToTeamButton = function(){
	
		//Highlight the starting positions this unit can be placed on
	
		//Throw the listener on
		jQuery( '.addUnitToTeamButton' ).click( function(){
			Unit_manageUnits.highlightSelectedUnit( this );
		});
		
	}
	
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
		
	}
	
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
				
	}
	
	//PUBLIC FUNCTION: handleEverything
	//The Pepper Potts function, in that it will just handle everything
	this.handleEverything = function(){
		Unit_manageUnits.handleAddToTeamButton();
		Unit_manageUnits.handleChangeTeam();
		Unit_manageUnits.handleChangeTeamName();
		Unit_manageUnits.handleNewTeamButton();
		Unit_manageUnits.handlePlaceUnit();
		Unit_manageUnits.handleRemoveTeam();
		Unit_manageUnits.loadTeamUnits();
	}
	
	//PUBLIC FUNCTION: handleNewTeamButton
	//Handle the button that allows the user to create a new team
	this.handleNewTeamButton = function(){
		
		//Remove the default handler
		jQuery( 'input[modelName="Team"][type="button"].editableSelectNew' ).unbind( 'click' );
		
		//Add our sick new handler
		jQuery( 'input[modelName="Team"][type="button"].editableSelectNew' ).each( function(){
			
			if( ! jQuery(this).isBound( 'click', Unit_manageUnits.addNewTeam ) ){
				jQuery(this).click(
					Unit_manageUnits.addNewTeam
				);
			}
			
		});
		
	}
	
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
		
	}
	
	//PUBLIC FUNCTION: handleRemoveFromTeamButton
	//Handles someone clicking on the button to remove the given unit from
	//their team
	this.handleRemoveFromTeamButton = function(){
		
		//Don't add duplicates
		jQuery('.removeFromTeam').each( function( index ){
			if( ! jQuery( this ).isBound( 'click', Unit_manageUnits.removeUnitFromTeam ) ){
				//Throw the listener on
				jQuery( this ).click(
					Unit_manageUnits.removeUnitFromTeam
				);
			}
		});
		
	}
	
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
		
	}
	
	//PUBLIC FUNCTION: highlightSelectedUnit
	//Highlight the row of the unit that was selected to be added
	//Ideally this will eventually incorporate the display of the unit
	//card and all that other fun shit.
	this.highlightSelectedUnit = function( selectedUnit ){
		
		//Grab the selected unit type UID
		Unit_manageUnits.selectedUnitTypeUID = jQuery( selectedUnit ).attr('uid');
		
		//Highlight anything to do with that unit
		jQuery( '[modelName="Unit"][uid="' + Unit_manageUnits.selectedUnitTypeUID + '"]' ).addClass( 'Unit_manageUnits_selectedUnit' );
		
	}
	
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
				Unit_manageUnits.refundUnitPool();
				Unit_manageUnits.populateTeamUnits( jSONData );
				Unit_manageUnits.displayTeamUnits( jSONData );
				Unit_manageUnits.handleRemoveFromTeamButton();
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
	
	//PUBLIC FUNCTION: placeSelectedUnit
	//Places the currently selected unit into the given position
	//on the given team.
	this.placeSelectedUnit = function( triggeringEvent ){
		
		//Make sure we have a selected unit, if we do then continue,
		//otherwise do nothing
		if( Unit_manageUnits.selectedUnitTypeUID != false ){

			//Add the selected unit, passing the selected tile as a parameter
			Unit_manageUnits.addUnitToTeam( triggeringEvent.target );
			
		}
		
	}
	
	//PUBLIC FUNCTION: populateTeamUnits
	//Build the HTML that will actually display the team units
	this.populateTeamUnits = function( jSONData ){
		
		//Team Cost 
		var teamCost = 0;
		
		//Loop through the jSONData returned to grab each unit
		jQuery.each( jSONData['unitsOnTeam'], function( key, unitData ){
			
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
		Unit_manageUnits.debitUnitPool( jSONData );
		
	}
	
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
		
		//Now with everything refunded we remove the table rows
		jQuery( 'div.teamUnits > table > tbody > tr[modelName="Unit"]' ).remove();
		
	}
	
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
		
	}
	
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
			
	}
	
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