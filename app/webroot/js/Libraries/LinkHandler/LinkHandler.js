//Handles link clicks and loads content for local pages instead of reloading the whole page
define(
	[
		'jquery'
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

				//Navigate to the link
				this.navigateToLink(link);

			},

			/**
			 * Check if the given link has a parameter
			 * @param link
			 * @returns {boolean}
			 */
			hasParams: function(link) {
				return link.indexOf('?') !== -1;
			},

			/**
			 * Initialize the link handler
			 */
			initialize: function() {

				var linkHandler = this;

				//Add a listener to every link that gets added to the page
				jQuery('body').on( 'click', 'a', function(event){
					linkHandler.handleLink(event)
				});

				//Add a listener to handle the browser navigation buttons
				window.addEventListener('popstate', function(){
					linkHandler.navigateToLink(window.location.pathname);
				});
			},

			/**
			 * Determines if a given link belongs to this site according to the
			 * window.Paths.webroot variable
			 * @param link
			 * @returns {boolean}
			 */
			isLinkLocal: function(link) {
				return link.indexOf(window.Paths.webroot) === 0;
			},

			/**
			 * Navigates the page to the given link (either by loading content through
			 * an ajax call or navigating to the next page)
			 * @param link
			 */
			navigateToLink: function(link) {

				//Determine if the link is a local link, if it is we need to load the content
				//into the DOM and add a parameter stating that we don't need all the other junk
				//This should cut down on requests to the server and overhead loading subsequent pages
				//Something about response time being good and all that.
				if( this.isLinkLocal(link) ){

					//If the link already contains parameters then we need to add to them
					//otherwise we create and add the only parameter
					var contentLink = link + '?requestType=content';
					if( this.hasParams(link) ){
						contentLink = link + '&requestType=content';
					}

					//If the link is local then we need to load it's content
					jQuery('#content').load(
						contentLink
					);
					//Push the histor state
					history.pushState(null, null, link);

				//If it is not a local link then redirect
				}else{
					window.location.href = link;
				}

			}

		};

		window.LinkHandler.initialize();

	}
);