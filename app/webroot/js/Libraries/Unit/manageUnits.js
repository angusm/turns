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
	
	//PUBLIC FUNCTION: handleEverything
	//The Pepper Potts function, in that it will just handle everything
	this.handleEverything = function(){
		Unit_manageUnits.handleAddToTeamButton();
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
	
}