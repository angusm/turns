
//Once the document is ready, launch everything
jQuery(document).ready( function(){
   var disclosure = new Disclosure();
   disclosure.handleDisclosure();
});

//Handles divs that, when clicked on, show and hide other divs
//Otherwise known as progressive disclosure
function Disclosure(){

    //Setup an array to keep track of what's closed and what isn't
    this.isHidden = [];

}

Disclosure.prototype = {

    //FUNCTION: getDisclosureStatusEstimate
    //Sometimes we don't already know if a disclosure name should be displayed or not
    //So in that case we look at the first element with that disclosure name and the disclosureDiv
    //class, that does not have the independentDisclosure class ( the independentDisclosure class
    //allows a disclosure to toggle independently of the rest of the similarly named disclosures )
    //and then we see whether or not the element is hidden and use that as our baseline
    getDisclosureStatusEstimate:function( disclosureName ){

        var disclosureForEstimate   = jQuery( '.disclosureDiv[disclosureName="' + disclosureName + '"]:not(.independentDisclosure)').first();
        var displayStatus           = jQuery(disclosureForEstimate).css('display');
        var isHidden                = jQuery(disclosureForEstimate).hasClass('hidden');

        //Return true if the element is hidden, either through display status or the hidden class
        return ('none' == displayStatus || isHidden);

    },

    //FUNCTION: handleDisclosure
    //Sets up management of disclosure divs so that they're display is toggled as necessary
    handleDisclosure:function(){

        var self = this;

        //Attach all the nice events to everything
        jQuery(document).each( function(){

            //Setup handling of clicks on the disclosure toggles
            jQuery(this).on(
                'click.disclosure',
                '.disclosureToggle',
                function(e){
                    self.toggleDisclosure(e.target);
                });

            //Setup the handling of disclosure controls that will only show and never
            //hide their given disclosure
            jQuery(this).on(
                'click.disclosure',
                '.disclosureShowButton',
                function(e){
                    self.showDisclosure(e.target);
                }
            );

        });

        //Setup default values and synchronize disclosure divs
        this.synchronizeDisclosures();

    },

    //FUNCTION: synchronizeDisclosures
    //Go through all the disclosures and make sure that they're matched up with the
    //way they're supposed to be.
    synchronizeDisclosures:function(){

        var self = this;

        jQuery( '.disclosureDiv' ).each( function(){

            //Get the disclosure name
            var disclosureName = jQuery(this).attr('disclosurename');
            if( disclosureName.split('_opposite').length > 1 ){
                disclosureName = disclosureName.split('_opposite')[0];
            }

            //Find out if we want to toggle everything to be hidden or show
            if( typeof self.isHidden[disclosureName] === 'undefined' ){
                self.isHidden[disclosureName] = self.getDisclosureStatusEstimate(disclosureName);
            }

            //Go through all the non-independent disclosure divs that don't match the current status, and fix them
            var nonIndependentMisfitSelector 			= '.disclosureDiv[disclosureName="' + disclosureName + '"]';
            var nonIndependentOppositeMisfitSelector 	= '.disclosureDiv[disclosureName="' + disclosureName + '_opposite"]';

            if( self.isHidden[disclosureName] ){
                nonIndependentMisfitSelector 			+= ':visible';
                nonIndependentOppositeMisfitSelector 	+= ':hidden';
                jQuery( nonIndependentMisfitSelector ).hide();
                jQuery( nonIndependentOppositeMisfitSelector ).show();
            }else{
                nonIndependentMisfitSelector 			+= ':hidden';
                nonIndependentOppositeMisfitSelector 	+= ':visible';
                jQuery( nonIndependentMisfitSelector ).show();
                jQuery( nonIndependentOppositeMisfitSelector ).hide();
            }


        });

    },

    //FUNCTION: toggleDisclosure
    //Toggles disclosure of the given disclosure name
    toggleDisclosure:function( disclosureToggle ){

        //Get the disclosure name
        var disclosureName = jQuery( disclosureToggle ).attr('disclosurename');

        //If we've got the _opposite trigger then we need to handle it
        if( 1 < disclosureName.split('_opposite').length ){
            disclosureName = disclosureName.split('_opposite')[0];
        }

        //Find out if we want to toggle everything to be hidden or show
        if( 'undefined' === typeof this.isHidden[disclosureName] ){
            this.isHidden[disclosureName] = this.getDisclosureStatusEstimate(disclosureName);
        }

        //Toggle the isHidden value
        this.isHidden[disclosureName] = ! this.isHidden[disclosureName];

        //Toggle visibility of the disclosure divs
        jQuery( '.disclosureDiv[disclosureName="' + disclosureName + '"]' ).toggle();
        jQuery( '.disclosureDiv[disclosureName="' + disclosureName + '_opposite"]' ).toggle();
        jQuery( '.disclosureArrow[disclosureName="' + disclosureName + '"]' ).toggleClass('hidden');
        jQuery( '.disclosureArrow[disclosureName="' + disclosureName + '_opposite"]' ).toggleClass('hidden');

        this.synchronizeDisclosures();

    }
};