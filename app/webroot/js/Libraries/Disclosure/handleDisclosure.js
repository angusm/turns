
//FUNCTION: getDisclosureStatusEstimate
//Sometimes we don't already know if a disclosure name should be displayed or not
//So in that case we look at the first element with that disclosure name and the disclosureDiv
//class, that does not have the independentDisclosure class ( the independentDisclosure class
//allows a disclosure to toggle independently of the rest of the similarly named disclosures )
//and then we see whether or not the element is hidden and use that as our baseline
function getDisclosureStatusEstimate( disclosureName ){

	var displayStatus = jQuery( '.disclosureDiv[disclosureName="' + disclosureName + '"]:not(.independentDisclosure)' ).
		css('display');

	var isHidden = jQuery( '.disclosureDiv[disclosureName="' + disclosureName + '"]:not(.independentDisclosure)' ).
		hasClass('hidden');

	if( displayStatus == 'none' || isHidden ){
		return true;
	}else{
		return false;
	}

}

//FUNCTION: handleDisclosure
//Sets up management of disclosure divs so that they're display is toggled as necessary
function handleDisclosure(){

	//Remove any previous handling
	jQuery('.disclosureToggle' ).unbind('click');

	//Setup handling of clicks on the disclosure toggles
	jQuery( '.disclosureToggle' ).click( function(){
		toggleDisclosure(this);
	});

	//Setup default values and synchronize disclosure divs
	synchronizeDisclosures();

}

//FUNCTION: synchronizeDisclosures
//Go through all the disclosures and make sure that they're matched up with the
//way they're supposed to be.
function synchronizeDisclosures( ){

	jQuery( '.disclosureDiv' ).each( function(){

		//Get the disclosure name
		var disclosureName = jQuery(this).attr('disclosurename');
		if( disclosureName.split('_opposite').length > 1 ){
			disclosureName = disclosureName.split('_opposite')[0];
		}

		//Find out if we want to toggle everything to be hidden or shown
		eval(
			'if( typeof window.' + disclosureName + '_disclosureHidden === "undefined" ){' +
				'window.' + disclosureName + '_disclosureHidden = getDisclosureStatusEstimate("' + disclosureName + '");' +
			'}' +
			'var disclosureHidden = window.' + disclosureName + '_disclosureHidden;'
		);

		//Go through all the non-independent disclosure divs that don't match the current status, and fix them

		var nonIndependentMisfitSelector 			= '.disclosureDiv[disclosureName="' + disclosureName + '"]';
		var nonIndependentOppositeMisfitSelector 	= '.disclosureDiv[disclosureName="' + disclosureName + '_opposite"]';

		if( disclosureHidden ){
			nonIndependentMisfitSelector 			+= ':visible';
			nonIndependentOppositeMisfitSelector 	+= ':hidden'
			jQuery( nonIndependentMisfitSelector ).hide();
			jQuery( nonIndependentOppositeMisfitSelector ).show();
		}else{
			nonIndependentMisfitSelector 			+= ':hidden';
			nonIndependentOppositeMisfitSelector 	+= ':visible'
			jQuery( nonIndependentMisfitSelector ).show();
			jQuery( nonIndependentOppositeMisfitSelector ).hide();
		}


	});

}

//FUNCTION: toggleDisclosure
//Toggles disclosure of the given disclosure name
function toggleDisclosure( disclosureToggle ){

	//Get the disclosure name
	var disclosureName = jQuery( disclosureToggle ).attr('disclosurename');
	if( disclosureName.split('_opposite').length > 1 ){
		disclosureName = disclosureName.split('_opposite')[0];
	}

	//Find out if we want to toggle everything to be hidden or shown
	eval(
			'if( typeof window.' + disclosureName + '_disclosureHidden === "undefined" ){' +
				'window.' + disclosureName + '_disclosureHidden = getDisclosureStatusEstimate("' + disclosureName + '");' +
			'}' +
			'window.' + disclosureName + '_disclosureHidden = ! window.' + disclosureName + '_disclosureHidden;' +
			'var disclosureHidden = window.' + disclosureName + '_disclosureHidden;'
	);

	//Toggle visibility of the disclosure divs
	jQuery( '.disclosureDiv[disclosureName="' + disclosureName + '"]' ).toggle();
	jQuery( '.disclosureDiv[disclosureName="' + disclosureName + '_opposite"]' ).toggle();
	jQuery( '.disclosureArrow[disclosureName="' + disclosureName + '"]' ).toggleClass('hidden');
	jQuery( '.disclosureArrow[disclosureName="' + disclosureName + '_opposite"]' ).toggleClass('hidden');

	//Go through all the non-independent disclosure divs that don't match the current status, and fix them

	var nonIndependentMisfitSelector 			= '.disclosureDiv[disclosureName="' + disclosureName + '"]';
	var nonIndependentOppositeMisfitSelector 	= '.disclosureDiv[disclosureName="' + disclosureName + '_opposite"]';

	if( disclosureHidden ){
		nonIndependentMisfitSelector 			+= ':visible';
		nonIndependentOppositeMisfitSelector 	+= ':hidden'
		jQuery( nonIndependentMisfitSelector ).hide();
		jQuery( nonIndependentOppositeMisfitSelector ).show();
	}else{
		nonIndependentMisfitSelector 			+= ':hidden';
		nonIndependentOppositeMisfitSelector 	+= ':visible'
		jQuery( nonIndependentMisfitSelector ).show();
		jQuery( nonIndependentOppositeMisfitSelector ).hide();
	}

}