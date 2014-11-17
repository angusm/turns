//Handles link clicks and loads content for local pages instead of reloading the whole page
require(
	[
		'//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'
	],
	function(){

		//Setup the object for handling links
		window.LinkHandler = {
			/**
			 * Handle the clicking of links on the page
			 * @param event
			 */
			handleLink: function(event){

				//Prevent the link from being followed directly
				event.preventDefault();

				//Get the link address
				var link = jQuery(event.target).attr('href');

				//Determine if the link is a local link, if it is we need to load the content
				//into the DOM and add a parameter stating that we don't need all the other junk
				//This should cut down on requests to the server and overhead loading subsequent pages
				//Something about response time being good and all that.
				if( this.isLinkLocal(link) ){

					//If the link already contains parameters then we need to add to them
					//otherwise we create and add the only parameter
					if( this.hasParams(link) ){
						link += '&requestType=content';
					}else{
						link += '?requestType=content';
					}

					//If the link is local then we need to load it's content
					jQuery('#content').load(
						link
					);

				//If it is not a local link then redirect
				}else{
					window.location.href = link;
				}
			},

			hasParams: function(link) {
				return link.indexOf('?') !== -1;
			},

			/**
			 * Determines if a given link belongs to this site according to the
			 * window.Paths.webroot variable
			 * @param link
			 * @returns {boolean}
			 */
			isLinkLocal: function(link) {
				return link.indexOf(window.Paths.webroot) === 0;
			}

		};

		//Add a listener to every link that gets added to the page
		jQuery('a').on( 'click', function(event){
			window.LinkHandler.handleLink(event)
		});

	}
);