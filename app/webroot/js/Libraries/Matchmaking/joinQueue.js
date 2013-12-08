// JavaScript Document

//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all

var Matchmaking_joinQueue = new joinQueue();
Matchmaking_joinQueue.handleEverything();

//Alright let's do this matchmaking stuff
function joinQueue(){
	
	//PUBLIC FUNCTION: handleEverything
	//Just be Pepper Potts already, do everything
	this.handleEverything = function(){
		
		//Handle the button
		Matchmaking_joinQueue.handleJoinQueueButton();
		
	}
	
	//PUBLIC FUNCTION: handleJoinQueueButton
	//Handle getting everything rolling when the user hits the join queue
	//(i.e. "Play Now") button.
	this.handleJoinQueueButton = function(){
	
		//Change the units displayed in the team pool and adjust what's shown in the unit pool
		jQuery( '.joinQueueButton' ).each( function(){
			
			if( ! jQuery(this).isBound( 'click', Matchmaking_joinQueue.joinQueue ) ){
				jQuery(this).bind( 
					'click',
					Matchmaking_joinQueue.joinQueue
				);
			}
			
		});
	
	}
	
	//PUBLIC FUNCTION: joinQueue
	//Make the call that's going to actually put the user in the queue
	this.joinQueue = function(){
	
		//Let's get this thing rolling
		var teamUID = jQuery( '.queueTeamSelect' ).val();
	
		//Make the necessary call
		jQuery.getJSON(
			homeURL + '/MatchmakingQueues/joinQueue/', 
			{
				teamUID:teamUID
			},
			function( jSONData ){
				
				console.log( jSONData );
				jQuery('.joinQueueButton').trigger( 'joinedQueue' );
								
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