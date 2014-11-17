<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Create a class to help with KendoUI shit
 * Class KendoUIHelper
 */
class KendoUIHelper extends AppHelper{

	//We'll be using some of the HTML helper's functionality to do awesome stuff
	var $helpers = ['Html'];

	const BELONGS_TO_RELATIONSHIP   = 'belongsTo';
	const FIELD_RELATIONSHIP        = 'field';
	const HAS_MANY_RELATIONSHIP     = 'hasMany';

	public function exportResults(
		$data,
		$variableName   = 'kendoVar',
		$arrayName      = 'data'
	){

		//Establish the variable assignment
		$content = 'require('.
			'['.
				'"//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js",'.
				'"//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"'.
			'],'.
			'function() {'.
				'require('.
					'['.
						'window.Paths.jsDir + "KendoUI/kendo.ui.core.min.js",'.
						'window.Paths.jsDir + "KendoModels/KendoModels.js"';

		//Loop through the models we need and bring in their Kendo models
		foreach( $this->getModelList($data) as $modelName ){
			$content .= ',window.Paths.jsDir + "KendoModels/'.$modelName.'.js"';
		}


		//Setup the variable
		$content .= '],'.
					'function(ddd){'.
			'console.log(ddd);'.
			'console.log(window.KendoModels);'.
			'console.log(window.KendoModels.Unit);'.
						'var '.$variableName.' = kendo.observable({'.
			$arrayName.': [';

		//Start looping through the data and getting it set up
		$content .= $this->getJSObjects($data);

		//Remove the trailing comma
		$content = rtrim($content,',');

		$content .= ']'.
			'});'.
			'});'.
			'});';

		//Return the tag
		return $this->Html->tag(
			'script',
			$content,
			[
				'type' => 'text/javascript'
			]
		);

	}

	/**
	 * Return a js set of results
	 * @param array $data
	 * @return string
	 */
	public function getJSObjects(array $data) {

		$content = '';

		//Loop through the data and start building the set, recursively
		//calling when necessary
		foreach( $data as $key => $value ){

			//If the key is numeric then we're starting with the results of a find all
			//and need to go a level deeper
			if (is_numeric($key)) {
				$content .= $this->getJSObjects($value);
				continue;
			}

			/**
			 * Determine what type of value we're about to build,
			 * if it's an an array built exclusively of arrays then it is
			 * the hasMany relationship to the current model. If it's any other type
			 * of an array then it's a belongsTo relationship to the current model.
			 * If it's not an array then it's a field/value of the current model.
			 */
			switch( $this->getRelationshipType($value) ) {

				case self::BELONGS_TO_RELATIONSHIP:
					$content .= 'new window.KendoModels.'.$key.'({'.
						$this->getJSObjects($value).
						'}),';
					break;

				case self::FIELD_RELATIONSHIP:
					$content .= $key.': "'.$value.'",';
					break;

				case self::HAS_MANY_RELATIONSHIP:

					$content .= Inflector::pluralize($key).': ['.
						$this->getJSObjects($value).
						'],';
					break;
			}

		}

		return $content;

	}

	/**
	 * Return the names of the models as values in an array
	 * @param array $data
	 * @return array
	 */
	public function getModelList(array $data){

		//Gather the model names as a set of keys, flip them and return them
		return array_keys($this->getModelListAsKeys($data));

	}

	/**
	 * Return the names of the models as keys in an array
	 * @param array $data
	 * @return array
	 */
	private function getModelListAsKeys(array $data, array $modelList=[]){

		//Loop through the data and add the necessary model names
		foreach($data as $key => $value) {
			if (is_array($value)) {
				if (!is_numeric($key) && !array_key_exists($key,$modelList)) {
					//Check the type
					switch ($this->getRelationshipType($value)) {
						case self::BELONGS_TO_RELATIONSHIP:
						case self::HAS_MANY_RELATIONSHIP:

							//Storing the model names as keys so as to
							//avoid the duplicates
							$modelList[$key] = true;
							break;
					}
				}
				$modelList = $this->getModelListAsKeys($value,$modelList);
			}
		}

		//Return the model names
		return $modelList;

	}

	/**
	 * Determine the relationship type value
	 * @param $value
	 * @return string
	 */
	public function getRelationshipType($value) {

		//Default to the field
		$relationshipType = self::FIELD_RELATIONSHIP;

		//If the value is an array
		if( is_array($value) ){
			//If each value in the array is also an array then it's a hasMany relationship,
			//otherwise it's a belongsTo relationship
			$relationshipType = self::HAS_MANY_RELATIONSHIP;
			foreach( $value as $subvalue ){
				if( ! is_array($subvalue) ){
					$relationshipType = self::BELONGS_TO_RELATIONSHIP;
					break;
				}
			}
		}

		return $relationshipType;

	}

}