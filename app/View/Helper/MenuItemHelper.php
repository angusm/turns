<?php

App::uses('AppHelper', 'View/Helper');

class MenuItemHelper extends AppHelper{

	//Bring in the helper we'll want to use
	var $helpers = ['Html'];

	/**
	 * Take in a MenuItem and return a link DOM element
	 * @param array $menuItemObject
	 * @return mixed
	 */
	public function link( array $menuItemObject ){

		//Return an HTML link
		return $this->Html->link(
			$menuItemObject['MenuItem']['name'],
			$this->htmlLinkHelperArray($menuItemObject)
		);
	}

	/**
	 * Taken in a MenuItem object and return the link array
	 * @param array $menuItemObject
	 * @return array
	 */
	public function htmlLinkHelperArray( array $menuItemObject ){

		//At a minimum we need a controller and an action
		$linkArray = [];
		$linkArray['controller']    = $menuItemObject['SiteLink']['controller'];
		$linkArray['action']        = $menuItemObject['SiteLink']['action'];

		//Then we have the option of having a ParameterSet
		if (
			array_key_exists('ParameterSet',$menuItemObject) &&
			array_key_exists('Parameter',$menuItemObject['ParameterSet'])
		) {
			//Add the parameters to the array
			foreach($menuItemObject['ParameterSet']['Parameter'] as $parameter){
				if(
					array_key_exists('key',$parameter) &&
					array_key_exists('value',$parameter)
				) {
					$linkArray[$parameter['key']] = $parameter['value'];
				}
			}
		}

		//Return the array
		return $linkArray;

	}

}