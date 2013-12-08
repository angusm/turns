// JavaScript Document

//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all

var Matchmaking_availableGameList = new availableGameList();
Matchmaking_availableGameList.handleEverything();

//Alright let's do this matchmaking stuff
function availableGameList(){

	//PUBLIC FUNCTION: addListElementsFromJSON
	//Add actual DOM elements from the jSON data that was received as 
	//a result of a get AvailableGamesList call 
	this.addListElementsFromJSON = function( jSONData ){
		
		//We'll build a list of the replacement HTML and then when we
		//have a complete list we'll replace the current contents to
		//keep the visual refresh time to a minimum.
		var nuListContent = ''

		//Grab all of the current games
		currentGames = jSONData.games;
		
		//Grab all of the pending matches
		pendingMatches = jSONData.pendingMatches;

		//Add a play button for each element
		jQuery.each( currentGames, function( gameIndex, gameObject ){
			
			nuListContent += Matchmaking_availableGameList.getGameElement( gameObject.UserGame );
			
		});
		
		//Add a pending match element
		jQuery.each( pendingMatches, function( pendingMatchIndex, pendingMatchObject ){
			
			nuListContent += Matchmaking_availableGameList.getPendingMatchElement( pendingMatchObject.MatchmakingQueue );
			
		});
		
		//And now that we have a nice list built, we swap the contents
		jQuery( 'div.availableGames' ).html( nuListContent );
		
	}
		
	//PUBLIC FUNCTION: getGameElement
	//Get an element for active games
	this.getGameElement = function( gameObject ){
	
		return '<div class="playGameButton"><a href="/turns/Games/playGame/'+gameObject.games_uid+'">Play Game '+gameObject.games_uid+'</a></div>';
		
	}
		
	//PUBLIC FUNCTION: getPendingMatchElement
	//Get an element for the user's entries in the matchmaking queue
	this.getPendingMatchElement = function( pendingMatchObject ){
	
		return '<div class="pendingMatchBlurb">Waiting for match...</div>';
		
	}
		
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
				
				//Finish updating the list with the data
				Matchmaking_availableGameList.addListElementsFromJSON( jSONData );
				
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