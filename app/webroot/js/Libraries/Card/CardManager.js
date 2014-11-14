
function CardManager() {

    //Setup a holder for the units queued to load in various elements
    this.queuedUnits = new Array();

    //Setup a spot for card data in the page data
    window.pageData.CardData = new Array();

    //FUNCTION: loadCardData
    this.loadCardData = function( unitTypeUID, elementIdentifier ){

        //Don't load a unit we're already loading, that'd be wasteful
        //Also if nothing was provided then don't load anything
        if (
            'undefined' !== typeof unitTypeUID &&
            'loading' != window.pageData.CardData[unitTypeUID]
        ) {

            //Set the unit's loading stance
            window.pageData.CardData[unitTypeUID] = 'loading';

            //Make a query to the server to get the relevant information for the card display
            jQuery.getJSON(
                window.Paths.webroot + 'UnitTypes/getCardViewData',
                {
                    unitTypeUID: unitTypeUID
                },
                function( jSONData ){

                    //Store the card data
                    window.pageData.CardData[unitTypeUID] = jSONData['cardViewData'];

                    //Load whatever is queued for this unit, this way old loads won't overwrite new requests
                    //but still allows for old loads to load up if nothing else has been requested, provided that
                    //we have an element we want to load and aren't just grabbing data
                    if( 'undefined' !== typeof elementIdentifier ){
                        Card_CardManager.showCardByUnitType( Card_CardManager.queuedUnits[elementIdentifier], elementIdentifier );
                    }
                }
            ).done( function(){

                }).fail( function(){

                }).always( function(){

                });

        }

    };

    //FUNCTION: showCardByUnitType
    //This will load the given card div with the given unit type's information
    this.showCardByUnitType = function( unitTypeUID, elementIdentifier ){

        //Set the given unit as the queued unit
        Card_CardManager.queuedUnits[elementIdentifier] = unitTypeUID;

        //Handle default initialization
	    unitTypeUID         = defaultValue( unitTypeUID, 4 );
	    elementIdentifier   = defaultValue( elementIdentifier, 'div.unitCard' );

        //Check if they unit is loaded into the page data already, if it's not then load it and come back to this function
        if( 'undefined' !== typeof window.pageData.CardData[unitTypeUID] && 'loading' != window.pageData.CardData[unitTypeUID] ){

            //Grab the element
            var cardDiv =  jQuery(elementIdentifier);

            //Grab the card data
            var cardData = window.pageData.CardData[unitTypeUID];

            //Setup some content holders
            var boardIcon           = '';
            var directionArrow      = '';
            var movementBox         = '';
            var damageBarToAdd      = '';
            var damageIcon          = '';
            var defenseBarToAdd     = '';
            var defenseIcon         = '';
            var teamCostBarToAdd    = '';
            var teamCostIcon        = '';

            //Start looping through the Unit Art Set properties
            jQuery.each( cardData['UnitArtSet']['UnitArtSetIcon'], function( key, unitArtSetIcon ){

                switch( unitArtSetIcon['Icon']['icon_positions_uid'] ){

                    //Board Icon
                    case '3':
                        boardIcon =
                            '<div class="boardIcon">' +
                            '<img src="'+window.Paths.imgDir+unitArtSetIcon['Icon']['image']+'" />' +
                            '</div>';
                        break;

                    //Damage Icon
                    case '4':
                        damageIcon = '<img class="attributePoint" src="'+window.Paths.imgDir+unitArtSetIcon['Icon']['image']+'" />';
                        break;

                    //Defense Icon
                    case '5':
                        defenseIcon = '<img class="attributePoint" src="'+window.Paths.imgDir+unitArtSetIcon['Icon']['image']+'" />';
                        break;

                    //Damage point bar
                    case '6':
                        damageBarToAdd = '';
                        for( var barCounter = 0; barCounter < cardData['UnitStat']['damage']; barCounter++ ){
                            damageBarToAdd += '<img class="attributePoint" src="'+window.Paths.imgDir+unitArtSetIcon['Icon']['image']+'" />';
                        }
                        damageBarToAdd += '<div class="attributeValue">'+cardData['UnitStat']['damage']+'</div>';
                        cardDiv.find('div.damageBar').html( damageBarToAdd );
                        break;

                    //Defense point bar
                    case '7':
                        defenseBarToAdd = '';
                        for( var barCounter = 0; barCounter < cardData['UnitStat']['defense']; barCounter++ ){
                            defenseBarToAdd += '<img class="attributePoint" src="'+window.Paths.imgDir+unitArtSetIcon['Icon']['image']+'" />';
                        }
                        defenseBarToAdd += '<div class="attributeValue">'+cardData['UnitStat']['defense']+'</div>';
                        cardDiv.find('div.defenseBar').html( defenseBarToAdd );
                        break;

                    //Grab the movement box icon
                    case '8':
                        movementBox = unitArtSetIcon['Icon']['image'];
                        break;

                    //Team cost point bar
                    case '9':
                        teamCostBarToAdd = '';
                        for( var barCounter = 0; barCounter < cardData['UnitStat']['teamcost']; barCounter++ ){
                            teamCostBarToAdd += '<img class="attributePoint" src="'+window.Paths.imgDir+unitArtSetIcon['Icon']['image']+'" />';
                        }
                        teamCostBarToAdd += '<div class="attributeValue">'+cardData['UnitStat']['teamcost']+'</div>';
                        break;

                    //Grab the direction arrow icon
                    case '10':
                        directionArrow  = unitArtSetIcon['Icon']['image'];
                        break;

                    //Team cost Icon
                    case '12':
                        teamCostIcon = '<img class="attributePoint" src="'+window.Paths.imgDir+unitArtSetIcon['Icon']['image']+'" />';
                        break;

	                default:
		                //Do nothing
		                break;

                }
            });

            //Setup the bars
            cardDiv.find( 'div.damageBar'   ).html( damageIcon      + damageBarToAdd );
            cardDiv.find( 'div.defenseBar'  ).html( defenseIcon     + defenseBarToAdd );
            cardDiv.find( 'div.teamcostBar' ).html( teamCostIcon    + teamCostBarToAdd );

            //Set the unit's name
            cardDiv.children('div.unitStatBox')
                .children('div.unitTypeName')
                .html(
                    cardData['UnitType']['name']
                );

            //Add the movement stat boxes and arrows for each movement set
            jQuery.each( cardData['UnitStat']['UnitStatMovementSet'], function( key, unitStatMovementSet ){

                //Setup a container for this movement set
                var movementSet = '<div class="movementSetDisplay">';

                jQuery.each( unitStatMovementSet['MovementSet']['Movement'], function( key, movement ){

                    //Setup the container for this direction set
                    movementSet += '<div class="movementDisplay">'
                                + '<div class="movementSpaces">'
                                + movement.spaces
                                + '</div>';

                    jQuery.each( movement['MovementDirectionSet'], function( key, movementDirectionSet ){
                        jQuery.each( movementDirectionSet['DirectionSet']['DirectionSetDirection'], function( key, direction ){

                            //Add the movement to the movement display
                            movementSet += '<img '
                                        + 'class="movementArrow'+direction['Direction'].name+'" '
                                        + 'src="'+window.Paths.imgDir+directionArrow+'"'
                                        + '/>'

                        });
                    });

                    //Close the movement display
                    movementSet += '</div>';

                });

                //Add the board icon and close the movement set display
                movementSet += boardIcon + '</div>';

                jQuery('div.movementClasses').html( movementSet );

            } );

        }else{

            //Load the unit data if it hasn't been loaded
            Card_CardManager.loadCardData( unitTypeUID, elementIdentifier );

        }

    }


}

