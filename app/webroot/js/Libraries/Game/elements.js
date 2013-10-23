// JavaScript Document

var Game_elements = null;

jQuery(document).ready( function(){
	
	Game_elements = new elements();
	Game_elements.handleEverything();

}); 

function elements(){
	//PUBLIC FUNCTION: arrangeElement
	//Arrange the given element
	this.arrangeElement = function( element ){
	
		var nuX = jQuery( element ).attr( 'x' ) * 70;
		var nuY = jQuery( element ).attr( 'y' ) * 70;
		
		jQuery( element ).css({
			"position"	: "absolute", 
			"left"		: nuX + "px",
			"top"		: nuY + "px"
		});
		
	}
	
	//PUBLIC FUNCTION: arrangeTiles
	//Arrange all of the gameplay tiles
	this.arrangeTiles = function(){
		
		//Loop through all of the tiles
		jQuery( '.gameTile' ).each( function(){
			
			Game_elements.arrangeElement( this );
			
		});
			
	}
	
	//PUBLIC FUNCTION: handleEverything
	//Setup the tiles
	this.handleEverything = function(){
		Game_elements.arrangeTiles();
	}
	
}