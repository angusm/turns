<?php

/**
 * Class FriendlyColumnName
 */
class FriendlyColumnName extends AppModel{

    //Setup the associations:
    //This model has none

    //CONSTRUCTOR
	/**
	 *
	 */
	public function __construct(){

        parent::__construct();

        //Setup an array we'll use for the validation of model_name, column_name and friendly_column_name
        $nameValidationArray = [
            'alphaNumeric'  => [
                'required'  => true,
                'rule'      => 'alphanumeric'
            ],
            'length'    => [
                'rule'      => [ 'maxLength', 64 ]
            ]
        ];

        //Setup the validation for the model
        $this->validate = [
            'model_name'            => $nameValidationArray,
            'column_name'           => $nameValidationArray,
            'friendly_column_name'  => $nameValidationArray
        ];

    }

    //FUNCTIONS

    //PUBLIC FUNCTION: getFriendlyName
    //Takes in a model name and column name and returns the friendly name for that column
	/**
	 * @param null $modelName
	 * @param null $columnName
	 * @return array
	 */
	public function getFriendlyName( $modelName=null, $columnName=null ){

        //Run the find
        $friendlyName = $this->find( 'first', [
            'conditions' => [
                'FriendlyColumnName.model_name'     => $modelName,
                'FriendlyColumnName.column_name'    => $columnName
            ],
            'fields'    => [
                'FriendlyColumnName.friendly_column_name'
            ]
        ]);

        //Return the friendly name
        return $friendlyName;

    }

    //PUBLIC FUNCTION: makeSchemaFriendly
    //Take in the schema from a given model and find friendly names for its columns if possible
	/**
	 * @param array $schema
	 * @param null $modelName
	 * @return array
	 */
	public function makeSchemaFriendly( $schema=[], $modelName=null ){

        //Start looping through the keys in the schema and grabbing the column names and establishing
        //the condition we'll use to grab all of the friendly names for the model at once.
        //Provided the query optimizer is working properly, doing this as one query will be much
        //faster than running each element in the schema individually.
        $columnConditions = [];
        foreach( $schema as $columnName => $schemaValues ){

            $columnConditions[] = [
                'FriendlyColumnName.column_name' => $columnName
            ];

        }

        //Now we run a search for records with the model name and any of the possible column names
        $friendlyNames = $this->find( 'all', [
            'conditions' => [
                'FriendlyColumnName.model_name'    => $modelName,
                'OR'            => $columnConditions
            ],
            'fields' => [
                'FriendlyColumnName.column_name',
                'FriendlyColumnName.friendly_column_name'
            ]
        ]);

        //Now we take our friendly names and plop them into their appropriate places in the schema and
        //return the result
        foreach( $friendlyNames as $friendlyName ){
            $schema[$friendlyName['FriendlyName']['column_name']]['friendly_column_name'] = $friendlyName['FriendlyName']['friendly_column_name'];
        }

        return $schema;
    }

}