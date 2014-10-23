// JavaScript Document
var Game_elements = null;

const GAME_ELEMENTS_TILE_HEIGHT = 9.9;
const GAME_ELEMENTS_TILE_WIDTH  = 14.9;

// Resize #my_div on browser resize
jQuery(window).resize(function() {
    Game_elements.resizeBoard();
});

/**
 * Creates a class to adjust the game's pieces
 * @constructor
 */
function GameElements() {
	//Establish the tile width and height
	this.tileHeight = GAME_ELEMENTS_TILE_HEIGHT;
	this.tileWidth  = GAME_ELEMENTS_TILE_WIDTH;
}

/**
 * Position the given element
 * @param element
 */
GameElements.prototype.arrangeElement = function( element ){

	//Establish some good old fashioned vars to *hopefully*
	//make the following equation easier to understand
	var xPos = jQuery( element ).attr( 'x' );
	var yPos = jQuery( element ).attr( 'y' );
	var halfTileWidth   = this.tileWidth / 2;
	var halfTileHeight  = this.tileHeight / 2;

	//Get the position of the element
    var nuX = (xPos - yPos + window.pageData.Game.Board.width - 1) * halfTileWidth ;
    var nuY = (xPos + yPos) * halfTileHeight;

	//Apply the CSS to adjust the element
	jQuery( element ).css({
		"position"	: "absolute",
		"left"		: nuX + "%",
		"top"		: nuY + "%"
	});

};

/**
 * Arrange everything that won't be moving later
 * @param gameUID
 */
GameElements.prototype.arrangeStaticElements = function( gameUID ){
    this.getBoard( gameUID );
	this.arrangeUnitCard();
};

/**
 * Arrange all of the gameplay tiles
 */
GameElements.prototype.arrangeTiles = function(){

	//Get a pointer to the class
	var gameElements = this;

	//Loop through all of the tiles
	jQuery('.gameTile' ).each( function(){
		gameElements.arrangeElement( this );

	});

};

/**
 * Put the unit card where the unit card belongs
 */
GameElements.prototype.arrangeUnitCard = function(){

	//Position the card properly
	jQuery( '.unitCard' ).each( function(){
		jQuery( this ).css({
			"position"	: "absolute",
			"left"		: "600px",
			"top"		: "20px"
		});
	});

};

/**
 * Create the board DOM elements
 */
GameElements.prototype.createBoard = function(){

    //Toss the board container on
	var gameBoardElement = jQuery(
		'div',
		{
			class: 'gameBoard'
		}
	);
    jQuery( 'div#content').append(gameBoardElement);

    //Toss the tiles on
    this.createTiles();

};

/**
 * Create DOM elements for each tile
 */
GameElements.prototype.createTiles = function(){

    //Start looping through and rendering the tiles
    for( var xCounter = 0; xCounter < window.pageData.Game.Board.width; xCounter++ ){
        for( var yCounter = 0; yCounter < window.pageData.Game.Board.height; yCounter++ ){

	        //Establish the color modulus
            var colorModulus = (xCounter + yCounter) % 2;
	        var gameTileElement = jQuery(
		        'div',
		        {
			        class:  'gameTile',
			        light:  colorModulus,
			        x:      xCounter,
			        y:      yCounter
		        }
	        );
            jQuery( 'div.gameBoard').append( gameTileElement );

        }
    }

    //Resize the board
    Game_elements.resizeBoard();

};

/**
 * Get the width and height of the board
 */
GameElements.prototype.getBoard = function(){

	var gameElements = this;

    //Make a request to the server and update all the variables
    jQuery.getJSON(
        homeURL + 'Games/getGameBoard',
        {
            gameUID: window.pageData.Game.uid
        },
        function( jSONData ){

            //Store the board information
            window.pageData.Game.Board = jSONData.board;
	        gameElements.createBoard();
	        gameElements.arrangeTiles();
            EventBus.dispatch( 'GAME_BOARD_CREATED', gameElements );

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

};

/**
 * Resize the board until it fits the window appropriately
 */
GameElements.prototype.resizeBoard = function(){

    var gameBoardDiv = jQuery( 'div.gameBoard' );

    if( window.innerHeight > window.innerWidth ){
        gameBoardDiv.height( window.innerWidth );
        gameBoardDiv.width(  window.innerWidth );
    }else{
        gameBoardDiv.height( window.innerHeight );
        gameBoardDiv.width(  window.innerHeight );
    }

};