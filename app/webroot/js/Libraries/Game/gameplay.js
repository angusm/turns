// JavaScript Document

//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all

var Game_gameplay = new gameplay();
Game_gameplay.handleEverything();

//Alright let's do this matchmaking stuff
function gameplay(){
	
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
			
			Game_gameplay.arrangeElement( this );
			
		});
			
	}
	
	//PUBLIC FUNCTION: arrangeUnit
	//Arrange the unit
	this.arrangeUnit = function( unitObjectPosition, unitObject ){
		
			console.log( unitObject );
		
		var element = jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' );
			
		var nuX = unitObject.x * 70;
		var nuY = unitObject.y * 70;
		
		jQuery( '.gameplayUnit[uid="' + unitObject.uid + '"]' ).css({
			"position"	: "absolute", 
			"left"		: nuX + "px",
			"top"		: nuY + "px"
		});
		
	}
	
	//PUBLIC FUNCTION: arrangeUnits
	//Arrange all of the gameplay units
	this.arrangeUnits = function(){
		
		//Loop through all of the units
		jQuery.each( window.enemyUnits,  Game_gameplay.arrangeUnit );
		jQuery.each( window.playerUnits, Game_gameplay.arrangeUnit );
			
	}
	
	//PUBLIC FUNCTION: handleEverything
	//Just be Pepper Potts already, do everything
	this.handleEverything = function(){
		Game_gameplay.arrangeTiles();
		Game_gameplay.arrangeUnits();
		Game_gameplay.handleUnitSelection();
	}
	
	//PUBLIC FUNCTION: handleUnitSelection
	//Handle selecting the unit when its clicked on
	this.handleUnitSelection = function(){
	
		//We only want to be adding this once
		jQuery( '.gameplayUnit' ).each( function(){
			
			if( ! jQuery(this).isBound( 'click', Game_gameplay.selectUnit ) ){
				jQuery(this).bind( 
					'click',
					Game_gameplay.selectUnit
				);
			}
			
		});
		
	}
	
	//PUBLIC FUNCTION: selectUnit
	//Select the given unit
	this.selectUnit = function( triggeredEvent ){
	
		//Click unit
		var clickedUnit = jQuery( triggeredEvent.target ).closest( '.gameplayUnit' );
		
		//Toggle the highlighted and selected units
		Game_gameplay.toggleHighlight( clickedUnit );
		Game_gameplay.toggleSelect( clickedUnit );
		
	}
	
	//PUBLIC FUNCTION: toggleHighlight
	//Toggle a highlight effect on the given element
	this.toggleHighlight = function( element ){
		
		//If this unit is highlighted unhighlight it, otherwise unhighlight
		//everything else then highlight it
		if( jQuery( element ).hasClass( 'highlighted' ) ){
			jQuery( element ).removeClass( 'highlighted' );
		}else{
			jQuery( '.highlighted' ).removeClass( 'highlighted' );
			jQuery( element ).addClass( 'highlighted' );
		}
		
	}
	
	//PUBLIC FUNCTION: toggleSelect
	//Toggle the select of the given element
	this.toggleSelect = function( element ){
		
		//Unselect other units and select them
		if( jQuery( element ).hasClass( 'selected' ) ){
			jQuery( element ).removeClass( 'selected' );
		}else{
			jQuery( '.selected' ).removeClass( 'selected' );
			jQuery( element ).addClass( 'selected' );
		}
		
	}
	
}