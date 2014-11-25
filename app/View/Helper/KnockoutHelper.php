<?php

//This class will be used to make all those sick as shit menu items that will run the show
App::uses('AppHelper', 'View/Helper');

/**
 * Class TurnMenuHelper
 */
class KnockoutHelper extends AppHelper {

	//We'll be using some of the HTML helper's functionality to do awesome stuff
	var $helpers = ['Html'];

	//Dump the data so we don't necessarily have to make an ajax call later when we set up
	public function dump ($id, $data) {
		return $this->Html->tag(
			'div',
			'',
			array(
				'class'     => 'data-dump',
				'data-dump' => json_encode($data),
				'data-name' => $id
			)
		);
	}
}