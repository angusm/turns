// JavaScript Document

var Game_elements = null;

jQuery(document).ready( function(){
	
	Game_elements = new elements();
	Game_elements.handleEverything();

}); 

function elements(){

    //Establish the tile width and height
    this.tileHeight  = 108;
    this.tileWidth   = 189;

	//PUBLIC FUNCTION: arrangeElement
	//Arrange the given element
	this.arrangeElement = function( element ){

		var xPos = jQuery( element ).attr( 'x' );
		var yPos = jQuery( element ).attr( 'y' );

        var nuX = (yPos * Game_elements.tileWidth  / 2 ) - (xPos * Game_elements.tileWidth  / 2 ) + ((window.pageData.Game.Board.width - 1) / 2 * Game_elements.tileWidth );
        var nuY = (xPos * Game_elements.tileHeight / 2 ) + (yPos * Game_elements.tileHeight / 2 ) + 150;
		
		jQuery( element ).css({
			"position"	: "absolute", 
			"left"		: nuX + "px",
			"top"		: nuY + "px"
		});
		
	}
	
	//PUBLIC FUNCTION: arrangeStaticElements
	//Arrange everything that wont' be moving later
	this.arrangeStaticElements = function( gameUID ){
        Game_elements.getBoard( gameUID );
	}

	//PUBLIC FUNCTION: arrangeTiles
	//Arrange all of the gameplay tiles
	this.arrangeTiles = function(){
		
		//Loop through all of the tiles
		jQuery( '.gameTile' ).each( function(){
			
			Game_elements.arrangeElement( this );
			
		});
			
	}
	
	//PUBLIC FUNCTION: arrangeUnitCard
	//Position the unit card properly
	this.arrangeUnitCard = function(){
		
		//Position the card properly
		jQuery( '.unitCard' ).each( function(){
			
			jQuery( this ).css({
				"position"	: "absolute", 
				"left"		: "600px",
				"top"		: "20px"
			});
		
		});
		
	}

    //PUBLIC FUNCTION: createBoard
    //Create the board DOM elements
    this.createBoard = function(){

        //Toss the board container on
        jQuery( 'div#content').append(
            '<div class="gameBoard"></div>'
        );

        //Toss the tiles on
        Game_elements.createTiles();

    }

    //PUBLIC FUNCTION: createTiles
    //Create the DOM elements for each tile
    this.createTiles = function(){

        //Start looping through and rendering the tiles
        for( var xCounter = 0; xCounter < window.pageData.Game.Board.width; xCounter++ ){
            for( var yCounter = 0; yCounter < window.pageData.Game.Board.height; yCounter++ ){

                var colorModulus = (xCounter + yCounter) % 2;

                jQuery( 'div.gameBoard').append(
                    '<div x="'+xCounter+'" y="'+yCounter+'" class="gameTile" light="'+colorModulus+'"></div>'
                )

            }
        }

    }

    //PUBLIC FUNCTION: getBoard
    //Get the width and height of the game board
    this.getBoard = function(){

        //Make a request to the server and update all the variables
        jQuery.getJSON(
            homeURL + 'Games/getGameBoard',
            {
                gameUID: window.pageData.Game.uid
            },
            function( jSONData ){

                //Store the board information
                window.pageData.Game.Board          = jSONData.board;

                Game_elements.createBoard();
                Game_elements.arrangeTiles();
                //Game_elements.arrangeUnitCard();

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

	//PUBLIC FUNCTION: handleEverything
	//Setup the tiles
	this.handleEverything = function(){
	}
	
}