//Setup the menuItem class
var MenuItem = function(){

	//VARIABLES
	this.openedItems        = { 0:null };
    this.menuItems          = [];

};

//FUNCTIONS
MenuItem.prototype = {

    //Grab all of the menu items
    loadMenuItems:function(){

        var self = this;

        //Empty main menu div
        jQuery('div#mainMenu').html('');

		jQuery.getJSON(
			homeURL + 'MenuItems/getAvailableMenuItems',
			{
				openMenuItems: this.openedItems
			},
			function( jSONData ){
				//Grab the menu items and set them up
                self.menuItems = jSONData.menuItems;
                self.setupMenu();
            }
		);
	},

    setupMenu:function(){


        var self = this;

        //Loop through each menu item and create the appropriate DIVs
        jQuery.each( this.menuItems, function( menuItemIndex, menuItem ){

            //Determine whether we want to append to the top level or as a
            //sub menu
	        var menuItemContainer = 'div#mainMenu';
            if( null !== menuItem['MenuItem']['parent_uid'] ){
                menuItemContainer = 'div' +
                    '.disclosureDiv' +
                    '.menuItemChildren' +
                    '[disclosureName="MenuItem'+menuItem['MenuItem']['parent_uid']+'"]';
            }

            //Setup the string for the parameter links UID
	        var parameterSetsAttribute = '';
            if( null !== menuItem['MenuItem']['parameter_sets_uid'] ){
                parameterSetsAttribute = ' parameter_sets_uid="'+menuItem['MenuItem']['parameter_sets_uid']+'"';
            }

            //Append the new menu item
            jQuery(menuItemContainer).append(
                '<div' +
                    ' class="menuItem disclosureToggle disclosureDiv"' +
                    ' disclosureName="MenuItem' + menuItem['MenuItem']['uid'] + '_opposite"' +
                    ' menuItemUID="'+menuItem['MenuItem']['uid']+'"' +
                    ' parentMenuItemUID="'+menuItem['MenuItem']['parent_uid']+'"'+
                    ' siteLinkUID="'+menuItem['MenuItem']['site_links_uid']+'"'+
                    parameterSetsAttribute +
                    '>' +
                    menuItem['MenuItem']['name'] +
                    '</div>' +

                    '<div' +
                    ' class="menuItem disclosureDiv"' +
                    ' disclosureName="MenuItem' + menuItem['MenuItem']['uid'] + '"' +
                    ' menuItemUID="'+menuItem['MenuItem']['uid']+'"' +
                    ' parentMenuItemUID="'+menuItem['MenuItem']['parent_uid']+'"'+
                    ' siteLinkUID="'+menuItem['MenuItem']['site_links_uid']+'"'+
                    ' style="display:none;"' +
                    parameterSetsAttribute +
                    '>' +
                    menuItem['MenuItem']['name'] +
                    '</div>' +

                    '<div' +
                    ' class="disclosureDiv menuItemChildren"' +
                    ' disclosureName="MenuItem' + menuItem['MenuItem']['uid'] + '"' +
                    ' style="display:none;"' +
                    '>' +
                    '</div>'
            );

        });

        jQuery(document).on(
            'click',
            'div.menuItem',
            function(e){
                self.handleMenuItemClick(e.target);
            }
        );

    },

    handleMenuItemClick:function( target ){

        //Grab the elements UID and find it in the menu items
        var uid         = jQuery(target).attr('menuitemuid');
        var menuItem    = null;


        //Now we start looping through all of the menu items to grab the parent and
        //the menu item in question
        jQuery.each( this.menuItems, function(index, value){

            //Grab the menu item and parent if applicable
            if( value['MenuItem']['uid'] == uid ){
                menuItem = value;
                return false;
            }

        });

        //Determine if we need to include the parameter set in the link
        var parameterSetsSuffix = '';
        if( null != menuItem['MenuItem']['parameter_sets_uid'] ){
            parameterSetsSuffix = '&parameter_sets_uid='+menuItem['MenuItem']['parameter_sets_uid']
        }

        //Hide all other menu item disclosures when this menu item is clicked and load the page
        //Any of the menu items that are expanded, should be contracted
        jQuery('.menuItem.disclosureToggle.disclosureDiv[menuitemuid!="'+menuItem['MenuItem']['uid']+'"][style="display: none;"]').trigger('click.disclosure');

        //We then work our way up through the target menu items parents and make sure they're expanded
        var parentItem = menuItem;
        while( null != parentItem['MenuItem']['parent_uid'] ){

            jQuery('.menuItem.disclosureToggle.disclosureDiv[menuitemuid="'+parentItem['MenuItem']['parent_uid']+'"]').trigger('click.disclosure');
            jQuery.each( this.menuItems, function( possibleParentIndex, possibleParent ){
                if( possibleParent['MenuItem']['uid'] == parentItem['MenuItem']['parent_uid'] ){
                    parentItem = possibleParent;
                    return false;
                }
            });

        }

        //Setup the URL we want to load into the browser's bar
        var contentURL =
            homeURL +
            'SiteLinks/getContent/?site_links_uid=' +
            menuItem['MenuItem']['site_links_uid']  +
            parameterSetsSuffix;

        var contentToLoadURL = contentURL + '&requestType=content';

        //Clear and then load the content into the main div
        jQuery('div#content').each( function(){
            jQuery(this).html('Loading...');
            jQuery(this).load(
                contentToLoadURL
            );
        });

        window.history.pushState("object or string", menuItem['MenuItem']['name'], contentURL);

        //Change the title stored in the page header
        jQuery('div#pageHeader').html(menuItem['MenuItem']['name']);

    }

};

//Setup the menu item variable
jQuery( document).ready( function(){
    var menuItem = new MenuItem();
    menuItem.loadMenuItems();
});
