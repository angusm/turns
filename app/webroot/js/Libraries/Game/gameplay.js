// JavaScript Document

//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all

var Game_gameplay = new gameplay();
Game_gameplay.handleEverything();

//Alright let's do this matchmaking stuff
function gameplay(){
	
	//PUBLIC FUNCTION: arrangeTile
	//Arrange the given tile
	this.arrangeTile = function( element ){
	
		var nuX = jQuery( element ).attr( 'x' ) * 50;
		var nuY = jQuery( element ).attr( 'y' ) * 50;
		
		jQuery( element ).css({
			"position"	: "absolute", 
			"top"		: nuX + "px",
			"left"		: nuY + "px",
		});
		
	}
	
	//PUBLIC FUNCTION: arrangeTiles
	//Arrange all of the gameplay tiles
	this.arrangeTiles = function(){
		
		//Loop through all of the tiles
		jQuery( '.gameTile' ).each( function(){
			
			Game_gameplay.arrangeTile( this );
			
		});
			
	}
	
	//PUBLIC FUNCTION: handleEverything
	//Just be Pepper Potts already, do everything
	this.handleEverything = function(){
		Game_gameplay.arrangeTiles();
	}
	
}