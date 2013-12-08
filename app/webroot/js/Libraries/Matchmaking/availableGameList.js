// JavaScript Document

//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all

var Matchmaking_availableGameList = new availableGameList();
Matchmaking_availableGameList.handleEverything();

//Alright let's do this matchmaking stuff
function availableGameList(){
	
	//PUBLIC FUNCTION: handleEverything
	//Just be Pepper Potts already, do everything
	this.handleEverything = function(){
		
		//Start checking for updates on the list
		setTimeout( Matchmaking_availableGameList.updateList, 1000 );
		
	}
	
	//PUBLIC FUNCTION: updateList
	//Update the game list with any changes
	this.updateList = function(){
		
		//Make the call to the 
		//Make the necessary call
		jQuery.getJSON(
			homeURL + 'UserGames/getAvailableGamesList/', 
			{
			},
			function( jSONData ){
				
				console.log( jSONData );
				
				//And call this again
				setTimeout( Matchmaking_availableGameList.updateList, 1000 );
							
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