
//Handles the display of the various slideshow images
var SlideshowClass = function(){

    //Set a variable to track the current slide
    this.currentSlideIndex  = 0;

    //Establish a slide interval, the time in ms to wait between slides
    this.slideInterval      = 8000;
    this.slideIntervalID    = null;

    //Setup an array of the slides themselves indexed by UID
    this.slides             = [];

    //Keep an ordered list of all the slides we want to cycle through
    this.slideUIDs          = [];

    var slideshow = this;

    //Add a handler to manage resizing
    jQuery( window ).resize( function(){
        slideshow.centerImages();
    });

};

SlideshowClass.prototype = {

    //FUNCTION: centerImages
    //Ensure the image stays centered when the window is resized
    centerImages:function(){

        //Loop through each image container and slide it
        jQuery('div#slides > div.slideImageContainer[slideID]').each( function(){
            //Center the image
            var nuX = ( jQuery(this).width()
                - jQuery('div#slides').width() )
                * (-1/2);

            //If the image is going to be offset the wrong way then we adjust it
            if( 0 < nuX ){
                nuX = 0;
            }

            jQuery(this).css({
                left : nuX
            });

        });

    },

    //FUNCTION: displaySlideAtIndex
    //Displays the slide at the given position in the slideUIDs index
    displaySlide:function(){

        //Store the this
        var slideshow = this;

        //Now we either need to load the slide or just display it
        //We want to display it
        if(
	        'undefined' !== typeof this.slides[this.slideUIDs[this.currentSlideIndex]] &&
            'loading' !== this.slides[this.slideUIDs[this.currentSlideIndex]]
        ){

            //Clear the interval for displaying the next slide to ensure the slide we
            //transitioned to has its own proper amount of time
            clearInterval( this.slideIntervalID );

            //Store the current slide in an easier to access variable
            var slide = this.slides[this.slideUIDs[this.currentSlideIndex]];

            //Throw a new div for the current slide in the slides container and then
            //fade out all the others
            var newSlideUID = new Date().getTime();

            //Add the new slide
            jQuery('div#slides').prepend(
                '<div class="slideImageContainer" slideID="'+newSlideUID+'">' +
                    '<img src="" />' +
                '</div>' +
                '<div class="slideContentContainer" slideID="'+newSlideUID+'">' +
                    '<div class="slideContent">' +
                        '<div class="slideHeader">' +
                            slide['name'] +
                        '</div>' +
                        slide['annotation'] +
                    '</div>' +
                '</div>'
            );

            //Load the image and then center it
            jQuery('div.slideImageContainer[slideID="'+newSlideUID+'"] > img')
                    .attr('src', imgURL+'Slides/'+slide['image'])
                    .load(function() {
                        slideshow.centerImages();
                    });

            //Fade out all the old slides
            jQuery('div#slides > div[slideID!="'+newSlideUID+'"]').fadeOut(
                (slideshow.slideInterval / 4),
                function(){
                    jQuery(this).remove();
                }
            );

            //Prepare for the next slide by loading it ahead of time
            var nextSlideIndex = this.currentSlideIndex + 1;
            if( nextSlideIndex > this.slideUIDs.length ){
                nextSlideIndex = 0;
            }
            this.loadSlide( this.slideUIDs[nextSlideIndex] );

            //Reset a new interval
            (function(self) {         //Self-executing func which takes 'this' as self
                return function() {   //Return a function in the context of 'self'
                    self.retrieve_rate(); //Thing you wanted to run as non-window 'this'
                }
            })(this);

            //So here we're setting an interval to make a callback to the nextSlide function but
            //we need to preserve the scope of 'this', so we set up an anonymous function that takes
            //'this' as a parameter and uses it to return a function that calls nextSlide in the
            //appropriate scope.
            this.slideIntervalID = setInterval(
                this.nextSlide.bind(this),
                this.slideInterval
            );

        }else{

            //As we did with set interval we have to create another anonymouse function to
            //run displaySlide in the proper context upon its callback
            this.loadSlide(
                this.slideUIDs[this.currentSlideIndex],
                this.displaySlide.bind(this)
            );

        }

    },

    //FUNCTION: getAvailableSlides
    //Grabs and populates the list of slide UIDs that are available to the
    //currently logged in user
    getAvailableSlides:function( callbackFunction ){

        var slideshow = this;

        //Make a JSON request and store the result
        jQuery.getJSON(
            homeURL + 'HeaderSlides/getAvailableSlideUIDs',
            {},
            function( jSONData ){

                //Store the slideUIDs
                slideshow.slideUIDs = jSONData['slideUIDs'];

                //Start loading the slides
                callbackFunction();

            }
        )
            .done(function(){})
            .fail(function(){})
            .always(function(){});
    },

    //FUNCTION: loadSlide
    //Promptly hit up the server to get the slide information necessary to
    //display it
    loadSlide:function( uid, callback ){

        //Don't load something that's already loaded
        if( 'undefined' === typeof this.slides[uid] ){

            //Set it to loading
            this.slides[uid] = 'loading';

            var slideshow = this;

            //Send in a JSON request
            jQuery.getJSON(
                homeURL+'HeaderSlides/getRecordData',
                {
                    uid: uid
                },
                function( jSONData ){

                    slideshow.slides[uid] = jSONData['HeaderSlide'];
                    if( 'undefined' !== typeof callback ){
                        callback();
                    }
                }
            )

        }

    },

    //FUNCTION: nextSlide
    //Moves to the next slide in the sequence
    nextSlide:function(){

        //Increment the current slide index, wrapping around if necessary
        this.currentSlideIndex++;
        if( this.currentSlideIndex >= this.slideUIDs.length ){
            this.currentSlideIndex = 0;
        }

        //Call the display function
        this.displaySlide();

    },

    //FUNCTION: startCycle
    //Start cycling through the list of available slides at a given rate
    startCycle:function(){

        //If we have no slides yet, we want to grab our slides
        if(
	        'undefined' === typeof this.slideUIDs.length ||
	        1 > this.slideUIDs.length
        ){
            this.getAvailableSlides( this.startCycle.bind(this) );
        }

        //Now we load the first slide
        this.nextSlide();

    }

};

jQuery( document).ready( function(){
    var Slideshow = new SlideshowClass();
    Slideshow.startCycle();
});