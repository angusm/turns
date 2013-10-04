//PEPPER POTTS FUNCTION: handleEverything

var Unit_manageUnits = new manageUnits();
Unit_manageUnits.handleEverything();

//Setup the object for managing units
function manageUnits(){
	
	//PUBLIC FUNCTION: addTeamRowForUnitTypeUID
	//Add a row to the team table for the given UnitTypeUID
	this.addTeamRowForUnitTypeUID = function( unitTypeUID ){
	
			
			//Grab the relevant data
			var unitName 	= jQuery( 'td[fieldName="name"][uid="'+unitTypeUID+'"]' ).html();
			var unitRow 	=  '<tr modelName="Unit" uid="' + unitTypeUID + '">';
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'uid', 		unitTypeUID );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'name', 	unitName );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitTypeUID, 'count', 	0 );
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
		var teamUID 	= jQuery( '.modelRecordSelect[modelname="Team"]' ).val();
		
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
			var unitUID		= unitData['Unit']['uid'];
			var unitCount 	= unitData['Unit']['count'];
						
			//Now we grab the count for the relevant element in the unit pool
			var poolCount	= jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitUID+'"]' ).attr("value");
			
			//Finally we calculate the debit count
			var debitedCount = poolCount - unitCount;
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitUID+'"]' ).attr("value", debitedCount);
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitUID+'"]' ).html(debitedCount);
						
						
		});
			
	}
	
	//PUBLIC FUNCTION: finalizeUnitAdd
	//Once we've gotten confirmation from the server we can actually show the add as completed to the user
	this.finalizeUnitAdd = function( jSONData ){
		
		//If the add was successful then update the display
		if( jSONData['success'] != false ){
			
			//Grab the unit type UID
			var unitTypeUID = jSONData['unitTypeUID'];
			
			//Update the pool count
			var originalPoolCount = jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedPoolCount = parseInt( originalPoolCount ) - 1;
			
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value", changedPoolCount);
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).html(changedPoolCount);
			
			//Ensure that a row for this unit exists in the team table
			if( jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).length == 0 ){
			
				//Add the given row
				Unit_manageUnits.addTeamRowForUnitTypeUID( unitTypeUID );
			}
			
			//Update the team count
			var originalTeamCount = jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedTeamCount = parseInt( originalTeamCount ) + 1;
			
			jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value", changedTeamCount);
			jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).html(changedTeamCount);
			
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
			var originalPoolCount = jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedPoolCount = parseInt( originalPoolCount ) + 1;
			
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value", changedPoolCount);
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).html(changedPoolCount);
			
			//Update the team count
			var originalTeamCount = jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value");
			var changedTeamCount = parseInt( originalTeamCount ) - 1;
			
			if( changedTeamCount == 0 ){
				jQuery( 'div.teamUnits > table > tbody > tr[uid="'+unitTypeUID+'"]' ).remove();
			}else{
				jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value", changedTeamCount);
				jQuery( 'div.teamUnits > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).html(changedTeamCount);
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
	
	//PUBLIC FUNCTION: handleEverything
	//The Pepper Potts function, in that it will just handle everything
	this.handleEverything = function(){
		Unit_manageUnits.handleAddToTeamButton();
		Unit_manageUnits.loadTeamUnits();
	}
	
	//PUBLIC FUNCTION: handleRemoveFromTeamButton
	//Handles someone clicking on the button to remove the given unit from
	//their team
	this.handleRemoveFromTeamButton = function(){
		
		//Remove extraneous listeners
		jQuery( '.removeUnitFromTeamButton' ).unbind( 'click' );
		//Throw the listener on
		jQuery( '.removeUnitFromTeamButton' ).click( function(){
			Unit_manageUnits.removeUnitFromTeam( this );
		});
		
	}
	
	//PUBLIC FUNCTION: loadTeamUnits
	//Load all the units on a team into the display box on the page
	this.loadTeamUnits = function(){
		
		//Get the controller name so that we're creating the right type 
		//of new record
		var teamUID = jQuery( '.modelRecordSelect[modelname="Team"]' ).val();
		
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
			var unitUID		= unitData['Unit']['uid'];
			var unitName 	= unitData['Unit']['UnitType']['name'];
			var unitCount 	= unitData['Unit']['count'];
			var unitRow 	=  '<tr modelName="Unit" uid="' + unitUID + '">';
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'uid', 		unitUID );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'name', 	unitName );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'count', 	unitCount );
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
		jQuery( 'div.teamUnits > td[fieldname="count"]' ).each( function(){
			
			//Get the count and unit type UID
			var teamCount	= jQuery(this).attr("value");
			var unitTypeUID = jQuery(this).attr("uid");
			
			//Now we grab the count for the relevant element in the unit pool
			var poolCount	= jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value");
			
			//Calculate and set the new pool count
			var refundedCount	= poolCount + teamCount;
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).attr("value", refundedCount);
			jQuery( 'div.unitPool > table > tbody > tr > td[fieldname="count"][uid="'+unitTypeUID+'"]' ).html(refundedCount);
			
			
		});
		
	}
	
	//PUBLIC FUNCTION: removeUnitFromTeam
	//Remove the unit from the selected team
	this.removeUnitFromTeam = function( unitElement ){
	
		//Grab the unit type UID and the team UID so we can make a call to the servers
		var unitTypeUID = jQuery( unitElement ).attr('uid');
		var teamUID 	= jQuery( '.modelRecordSelect[modelname="Team"]' ).val();
		
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
	
}