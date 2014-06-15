/**
 * Created by a.macdonald on 02/06/14.
 */
jQuery( document).ready( function(){

    //Adjust the main page content to reflect the size of the main menu
    jQuery( 'div#mainPage').css( 'margin-left', jQuery('div#mainMenu').width() + 'px' );

    //Hide the menu on a logo clikc
    jQuery(this).on(
        'click',
        'div#mainMenuToggle',
        function(){

            //Toggle the slid over classes
            jQuery('div#mainMenu').toggleClass('slidOver');
            jQuery('div#logo').toggleClass('slidOver');

            //Resize everything
            resizeEverything();
        }
    );

});

//Resize the page margin
jQuery( window ).resize( function(){
    resizeEverything();
});

//Resize the main page margin
function resizeEverything(){

    //Handle the main menu
    jQuery('div#mainMenu').each( function(){

        //Animate accordingly
        if( ! jQuery(this).hasClass('animating') ){

            //Assume a shown menu
            var nuLeft = 0;
            jQuery(this).addClass('animating');

            //Check if we want it shown or not
            if( jQuery(this).hasClass('slidOver') ){
                nuLeft = -1 * (jQuery(this).width() - 25);
            }

            jQuery(this).animate({
                'left': nuLeft
            },{
                'complete' : function(){ jQuery(this).removeClass('animating'); },
                'duration' : 200,
                'progress' : function(){ resizeEverything(); }
            });
        }

        //Establish the new margin
        var newMargin =
            jQuery(this).width() +
            parseInt( jQuery(this).css('left') );

        //Adjust the main page content to reflect the size of the main menu
        jQuery('div#mainPage').css(
            'margin-left',
            newMargin + 'px'
        );

    });

    //Handle the logo resize
    jQuery('div#logo').each( function(){

        //Only adjust it if it's not animating
        if( ! jQuery(this).hasClass('animating') ){

            //Add the animation
            jQuery(this).addClass('animating');

            var animationParams = {
                'max-width' : '200px',
                'min-width' : '100px',
                'width'     : '20%'
            };

            //Build the animation parameters
            if( jQuery(this).hasClass('slidOver') ){
                animationParams = {
                    'max-width' : '25px',
                    'min-width' : '25px',
                    'width'     : '25px'
                };
            }

            //Animate the logo
            jQuery(this).animate(
                animationParams,
                {
                    'complete' : function(){ jQuery(this).removeClass('animating'); },
                    'duration' : 200
                }
            );
        }

    });

}