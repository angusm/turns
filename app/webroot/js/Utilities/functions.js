/**
 * Created by Angus on 2014-11-23.
 */
/**
 * Used to assign a default value to a parameter in a readable way
 * @param parameter
 * @param defaultValue
 * @returns {*}
 */
function defaultValue (parameter, defaultValue) {
	var result = parameter;
	if ('undefined' === typeof parameter) {
		result = defaultValue;
	}
	return result;
}