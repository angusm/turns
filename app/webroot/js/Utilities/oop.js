/**
 * Created by Angus on 2014-11-23.
 */

/**
 * Extends classes, OOP YAY
 * @param childClass
 * @param parentClass
 */
function extend (childClass, parentClass) {
	childClass.prototype = new parentClass();
	childClass.prototype.constructor = childClass;
}