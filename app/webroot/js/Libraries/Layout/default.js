/**
 * Created by a.macdonald on 02/06/14.
 */
jQuery( document).ready( function(){

    //Adjust the main page content to reflect the size of the main menu
    jQuery( 'div#mainPage').css( 'margin-left', jQuery('div#mainMenu').width() + 'px' );

});

jQuery( window ).resize( function(){

    //Adjust the main page content to reflect the size of the main menu
    jQuery( 'div#mainPage').css( 'margin-left', jQuery('div#mainMenu').width() + 'px' );

});
