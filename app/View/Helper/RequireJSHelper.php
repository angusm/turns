<?php

//This class will be used to make all those sick as shit menu items that will run the show
App::uses('AppHelper', 'View/Helper');

/**
 * Class TurnMenuHelper
 */
class RequireJSHelper extends AppHelper {

	//We'll be using some of the HTML helper's functionality to do awesome stuff
	var $helpers = ['Html'];

	/**
	 * Return a link to the JS file in the library using the base URL
	 * @param $libraryLink
	 * @return string
	 */
	public function requireJSFromLib ($libraryLink) {
		return $this->requireJS('/Libraries'.$libraryLink);
	}

	/**
	 * Return a link to the JS file using the base URL
	 * @param $jsLink
	 * @return string
	 */
	public function requireJS ($jsLink) {
		return '"'.$this->Html->url(['controller' => 'js', 'action' => '']).$jsLink.'"';
	}
}