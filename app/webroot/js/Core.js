/**
 * Establish the requireJS configuration
 */
requirejs.config({
	baseUrl: '/turns/js/Libraries',
	paths: {
		'jquery': '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min',
		'jquery-ui': '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min',
		'kendoUI': 'KendoUI/kendo.ui.core.min',
		'knockout': 'Knockout/knockout-3.2.0'
	},
	shim: {
		'jquery': {
			exports: 'jQuery'
		},
		'jquery-ui': {
			deps: ['jquery'],
			exports: 'jquery-ui'
		},
		'kendoUI': {
			deps: [
				'jquery',
				'jquery-ui'
			],
			exports: 'kendo'
		},
		'knockout': {
			exports: 'ko'
		}
	},
	waitSeconds: 30000
});
/**
 * Load everything important
 */
requirejs(
	[
		'Authentication/authentication',
		'Disclosure/handleDisclosure',
		'EditableSelect/editableSelect',
		'Events/EventBus',
		'Inflection/inflection',
		'jquery',
		'jquery-ui',
		'LinkHandler/LinkHandler',
		'MenuItem/menuItem',
		'Utilities/functions',
		'Utilities/oop'
	],
	function () {

		/**
		 * Run everything we need to get going on
		 * @type {Authentication}
		 */
		var auth = new Authentication();
		var disclosure = new Disclosure();
		var EditableSelect_editableSelect = new EditableSelect();
		auth.handleEverything();
		disclosure.handleDisclosure();
		EditableSelect_editableSelect.handleEverything();

		/// ----------------------------- UTILITY FUNCTIONS ------------------------------------

		/**
		 * Pulled from this StackOverflow question:
		 * http://stackoverflow.com/questions/6361465/how-to-check-if-click-event-is-already-bound-jquery
		 * Check to see if the given function is bound to and element for the given eventType
		 * Commented with my best understanding as of Oct 5th 2013
		 * @param eventType
		 * @param callBackFunction
		 * @returns {boolean}
		 */
		jQuery.fn.isBound = function (eventType, callBackFunction) {

			//To do this we've got to grab some data from the jQuery library
			var eventData = jQuery._data(this[0], 'events');
			var returnValue = false;

			//If there's no event data then there can't be a bound function
			if (eventData === undefined) {
				return false;
			}

			//If there's nothing bound to the event, then our function can't be bound
			if (!jQuery.inArray(eventType, eventData) || eventData[eventType] == undefined) {
				return false;
			}

			//We now know there is functions bound the the given event, it's our job
			//to loop through them and see if any of them are the one we're looking for
			if (0 !== eventData[eventType].length) {
				jQuery.each(eventData[eventType], function (indexKey, value) {

					if (value['handler'] == callBackFunction) {
						returnValue = true;
					}

				});
			}

			//Return the result
			return returnValue;

		};

		/**
		 * Returns true if a thing is an Int
		 * @param something
		 * @returns {boolean}
		 */
		isInt = function (something) {
			var parseIntResult = parseInt(something);
			return !isNaN(parseIntResult) && isFinite(something);
		}

		/**
		 * Takes a given bit of text and makes is safe to pass to a URL in this case we
		 * convert spaces to underscores and remove anything else we can't handle
		 * @param dangerousWord
		 * @returns {string}
		 */
		makeURLSafe = function (dangerousWord) {
			dangerousWord = dangerousWord.replace(/ /g, '_');
			dangerousWord = dangerousWord.replace(/[^\w-]+/g, '');
			return dangerousWord;
		}

	}
);