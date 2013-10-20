//PEPPER POTTS FUNCTION: handleEverything

var Unit_manageUnits = new manageUnits();
Unit_manageUnits.handleEverything();

//Setup the object for managing units
function manageUnits(){
	
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
			var unitRow 	=  '<tr modelName="Unit" uid="' + unitTypeUID + '">';
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'uid', 		unitTypeUID );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'name', 	unitName );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'quantity', 0 );
			unitRow			+= '<td modelName="Unit">';
			unitRow			+= '<input type="button" value="<" class="removeUnitFromTeamButton"';
			unitRow			+= ' modelName="Unit" uid="' + unitTypeUID + '"></td>';
			unitRow			+= '</tr>';
			
			//Create the necessary element
			jQuery( 'div.teamUnits > table > tbody' ).append( unitRow );
			Unit_manageUnits.handleRemoveFromTeamButton();
		
		
	}
	
	//PUBLIC FUNCTION: addUnitToTeam
	//Add the unit to the selected team
	this.addUnitToTeam = function( unitElement ){
	
		//Grab the unit type UID and the team UID so we can make a call to the servers
		var unitTypeUID = jQuery( unitElement ).attr('uid');
		var teamUID 	= jQuery( '.editableSelect[modelname="Team"]' ).val();
		
		//Make the necessary call
		jQuery.getJSON(
			homeURL + '/TeamUnits/addUnitToTeamByUnitTypeUID/', 
			{
				teamUID:		teamUID,
				unitTypeUID:	unitTypeUID
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
			console.log( unitUID );
			console.log( poolCount );
			console.log( debitedCount );
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitUID+'"]' ).attr( "value", debitedCount );
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitUID+'"]' ).html( debitedCount );
						
						
		});
			
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
	this.finalizeUnitAdd = function( jSONData ){
		
		//If the add was successful then update the display
		if( jSONData['success'] != false ){
			
			//Grab the unit type UID
			var unitTypeUID = jSONData['unitTypeUID'];
			
			//Update the pool count
			var originalPoolCount = jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="quantity"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedPoolCount = parseInt( originalPoolCount ) - 1;
			
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
	
		//Throw the listener on
		jQuery( '.addUnitToTeamButton' ).click( function(){
			Unit_manageUnits.addUnitToTeam( this );
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
	
	//PUBLIC FUNCTION: handleRemoveFromTeamButton
	//Handles someone clicking on the button to remove the given unit from
	//their team
	this.handleRemoveFromTeamButton = function(){
		
		//Don't add duplicates
		jQuery('.removeUnitFromTeamButton').each( function( index ){
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
	
	//PUBLIC FUNCTION: populateTeamUnits
	//Build the HTML that will actually display the team units
	this.populateTeamUnits = function( jSONData ){
		
		//Loop through the jSONData returned to grab each unit
		jQuery.each( jSONData['unitsOnTeam'], function( key, unitData ){
			
			//Grab the relevant data
			var unitUID		= unitData['UnitType']['uid'];
			var unitName 	= unitData['UnitType']['name'];
			var unitCount 	= unitData['TeamUnit']['quantity'];
			var unitRow 	=  '<tr modelName="Unit" uid="' + unitUID + '">';
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'uid', 		unitUID );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'name', 	unitName );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'quantity', 	unitCount );
			unitRow			+= '<td modelName="Unit">';
			unitRow			+= '<input type="button" value="<" class="removeUnitFromTeamButton"';
			unitRow			+= ' modelName="Unit" uid="' + unitUID + '"></td>';
			unitRow			+= '</tr>';
			
			//Create the necessary element
			jQuery( 'div.teamUnits > table > tbody' ).append( unitRow );
			Unit_manageUnits.handleRemoveFromTeamButton();
						
		});
		
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
	
		var unitElement = triggeredEvent.target;
	
		//Grab the unit type UID and the team UID so we can make a call to the servers
		var unitTypeUID = jQuery( unitElement ).attr('uid');
		var teamUID 	= jQuery( '.editableSelect[modelname="Team"]' ).val();
		
		//Make the necessary call
		jQuery.getJSON(
			homeURL + '/TeamUnits/removeUnitFromTeamByUnitTypeUID/', 
			{
				teamUID:		teamUID,
				unitTypeUID:	unitTypeUID
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