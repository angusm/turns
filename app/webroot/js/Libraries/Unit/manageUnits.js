//PEPPER POTTS FUNCTION: handleEverything

var Unit_manageUnits = new manageUnits();
Unit_manageUnits.handleEverything();

//Setup the object for managing units
function manageUnits(){
	
	//PUBLIC FUNCTION: addUnitToTeam
	//Add the unit to the selected team
	this.addUnitToTeam = function( unitElement ){
	
		var unitTypeUID = jQuery( unitElement ).attr('uid');
		
		alert( unitTypeUID );
			
	}
	
	//PUBLIC FUNCTION: getUnitTDCell
	//Create a cell with some unit data in there
	this.getUnitTDCell = function( uid, name, value ){
		
		return  '<td modelName="Unit" ' +
				'uid="' + uid + '" ' +
				'fieldName="' + name + '" ' +
				'>' + value + '</td>';
				
		
	}
	
	//PUBLIC FUNCTION: handleEverything
	//The Pepper Potts function, in that it will just handle everything
	this.handleEverything = function(){
		Unit_manageUnits.handleAddToTeamButton();
		Unit_manageUnits.loadTeamUnits();
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
			var unitName 	= unitData['Unit']['name'];
			var unitCount 	= unitData['Unit']['count'];
			var unitRow 	=  '<tr modelName="Unit" uid="' + unitUID + '">';
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'uid', 		unitUID );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'name', 	unitName );
			unitRow			+= Unit_manageUnits.getUnitTDCell( unitUID, 'count', 	unitCount );
			unitRow			+= '<td modelName="Unit">';
			unitRow			+= '<input type="button" value=">" class="addUnitToTeamButton"';
			unitRow			+= ' modelName="Unit" uid="' + unitUID + '"></td>';
			unitRow			+= '</tr>';
			
			//Create the necessary element
			jQuery( 'div.teamUnits' ).append( unitRow );
						
		});
		
	}
	
}