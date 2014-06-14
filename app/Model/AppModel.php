<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	

	//We want all our data to be containable so that we're only
	//ever using what we need to 
	public $actsAs = array('Containable');
	
	//In the same spirit of containable we adjust the recursive value
	public $recursive = -1;
	
	//We want to use synthetic keys that couldn't possibly have any 
	//semantic information. If they ever do, they've been used wrong
	//and need to be FIXED!
	public $primaryKey = 'uid';

	//Set up some standard validation messages.	
	public static $alphaNumericMessage 	= 'Letters and numbers only please.';
	public static $booleanMessage 		= 'TRUE or FALSE only please.';
	public static $decimalMessage 		= 'Decimals only please.';
	public static $numericMessage 		= 'Numbers only please.';
	
	//Set up some standard regular expression rules
	public static $alphaNumericWithSpacesValidationRule	= array('custom',  '/^[a-z0-9 ]*$/i');

	//Handy universal functions

    //PUBLIC FUNCTION: afterSave
    //This is a CakePHP callback that runs after data is saved to the model
    //We're using it here to create an effective date record when necessary for new records
    public function afterSave( $created, $options = Array() ){

        //Call the parent function, always a good idea, respect your parents
        parent::afterSave( $created, $options );

        //Grab the datasource in case this construction fails and we need to roll
        //back whatever transaction this call may be a part of
        $dataSource = $this->getDataSource();

        //Grab the class name because we'll be using it a lot
        $className = get_class($this);

        //Get the model name for the effective date class that refers to the
        //current model
        $effectiveDateModelName = $className.'EffectiveDate';

        //Grab the saved original model
        $savedModel = $this->data;

        //Check to see that this class is associated with an effective dating scheme and that
        //we have a proper key in the options
        if( array_key_exists( $effectiveDateModelName, $this->hasMany ) &&
            array_key_exists( $className, $savedModel  ) &&
            array_key_exists( 'uid', $savedModel[$className] ) ){

            //Initialize the related model
            $effectiveDateModel = ClassRegistry::init($effectiveDateModelName);

            /*Establish the conditions to see if the effective date model already has an
            entry for the UID of the new record*/
            $conditions = array(
                $effectiveDateModelName.'.'.Inflector::tableize( $className ).'_uid' => $savedModel[$className]['uid']
            );

            //If no such effective date record exists then we create one
            if( ! $effectiveDateModel->hasAny($conditions) ){

                $effectiveDateModel->create();
                $effectiveDateModelData = array(
                    $effectiveDateModelName => array(
                        Inflector::tableize( $className ).'_uid' => $savedModel[$className]['uid'],
                        'start_date' => date('Y-m-d H:i:s')
                    )
                );
                $saveResult = $effectiveDateModel->save($effectiveDateModelData);

                //Do a rollback if there was a problem with the save
                if( ! array_key_exists( $effectiveDateModelName, $saveResult ) ||
                    ! array_key_exists( 'uid', $saveResult[$effectiveDateModelName]) ){
                    $dataSource->rollback();
                }

            }

        }

        return true;

    }

    //PUBLIC FUNCTION: beforeFind
    //This function is triggered by CakePHP before any data is returned
    //We use it hear to incorporate it with effective dating for models where necessary
    public function beforeFind( $query ){

        //Call the parent function, always a good idea, respect your parents
        parent::beforeFind( $query );

        //Get the model name for the effective date class that refers to the
        //current model
        $effectiveDateModelName = get_class($this).'EffectiveDate';

        //Add the effective dating conditions to the query if necessary
        //To do this we check the structure for an effective dating model relationship
        //We then grab all of the UIDs of any current effective dates and then
        //filter the results of the find based on these UIDs
        if( array_key_exists( $effectiveDateModelName, $this->hasMany ) ){

            //Grab the UIDs from the related model
            $effectiveDateModel = ClassRegistry::init($effectiveDateModelName);
            $modelUIDs = $effectiveDateModel->find( 'list', array(
                'conditions' => array(
                    $effectiveDateModelName.'.start_date <=' => date('Y-m-d H:i:s'),
                    'OR' => array(
                        $effectiveDateModelName.'.end_date >=' => date('Y-m-d H:i:s'),
                        $effectiveDateModelName.'.end_date' => null
                    )
                ),
                'fields' => array(
                    $effectiveDateModelName.'.'.Inflector::tableize( get_class($this) ).'_uid'
                )
            ));

            //Make sure we have conditions
            if( ! ( array_key_exists('conditions', $query) && is_array( $query['conditions'] ) ) ){
                $query['conditions'] = array();
            }

            //New we merge the condition on these UIDs into the main query
            $query['conditions'] = array_merge(
                $query['conditions'],
                array(
                    get_class($this).'.uid' => $modelUIDs
                )
            );

        }

        //Return the possibly modified query
        return $query;

    }

	//PUBLIC FUNCTION: createNewRecord
	//Create a new record in the database for this model
	//Chances are this will need to be overridden in the child model
	//assuming that the child model does any validation
	public function createNewRecord(){

		$modelName = get_class( $this );

		$modelFields = array();

		//Loop through the model's validation (if such validation exists)
		//Make sure we get the lil kid all set up to pass 
		if( isset( $this->validate ) ){
			foreach( $this->validate as $fieldName => $fieldCriteria ){
			
				//If we have a default we can set then we do
				if( isset( $fieldCriteria['default'] ) ){
					$modelFields[ $fieldName ] = $fieldCriteria['default'];
			  	}
				
			}
		}
		
		//Finalize the model data
		$modelData = array(
			$modelName => $modelFields
		);

		//Create the record
		$this->create();
		$this->save( $modelData );
                
		
		//Even though what we're actually returning the "uid" element
		//we need to use $this->id as it seems to be where Cake stores
		//the primary key value after a save. 
		return $this->id;
		
	}
	
	//PUBLIC FUNCTION: fixBooleans
	//Fix any boolean values that might be strings like "true"
	public function fixJSONValueBooleans( $jsonValues ){
	
		$fixedJSONValues = $jsonValues;
	
		//Loop through and check the validate array for anything 
		//we have to worry about
		foreach( $jsonValues as $fieldName => $fieldValue ){
			
			//Is there a rule set
			if( isset( $this->validate[$fieldName]['rule'] ) ){
				//Is it boolean?
				if( $this->validate[$fieldName]['rule'] == 'boolean' ){
					
					if( $fieldValue == 'true' ){
						$fixedJSONValues[$fieldName] = true;
					}else if( $fieldValue == 'false' ){
						$fixedJSONValues[$fieldName] = false;
					}
					
				}							
			}
			
		}
		
		return $fixedJSONValues;
		
	}
	
	//PUBLIC FUNCTION: getAssocationArray
	//Return useful information about the given association
	//array, such as the foreign key and class name.
	public function getAssociationArray( $modelName, $modelArray ){
			
		//Get the classname 
		if( isset( $modelArray['className'] ) ){
			$className = $modelArray['className'];
		}else{
			$className = $modelName;
		}
		
		//Get the foreign key
		if( isset( $modelArray['foreignKey'] ) ){
			$foreignKey = $modelArray['foreignKey'];
		//If we didn't set a new foreign key for the association
		//(Which is unlikely to be, since we want sick lil' synthetic
		//UIDs and are using MySQL workbench which maintains the plural
		//tablename in the foreign key) then we get the CakePHP default
		//foreign key format
		}else{
			$foreignKey = Inflector::underscore($className) . '_id';
		}
		
		return array( 
				'foreignKey' 	=> $foreignKey,
				'className'		=> $className
				);
	
	}
	
	//PUBLIC FUNCTION: getBelongsTo
	//Return the belongsTo array
	public function getBelongsTo(){
		return $this->belongsTo;
	}
	
	//PUBLIC FUNCTION: getBelongsToFieldsArray
	//Return an array containing the foreign key as the key and the 
	//associated model name as the value
	public function getBelongsToFieldsArray(){
		
		//Initialize the array
		$belongsToFields = array();
		
		//Loop through and create the
		foreach( $this->belongsTo as $associatedModelName => $innerArray ){
			
			$belongsToFields[$innerArray['foreignKey']] = $associatedModelName;
			
		}
		
		return $belongsToFields;
		
	}
	
	//PUBLIC FUNCTION: getHasMany
	//Return the hasMany array
	public function getHasMany(){
		return $this->hasMany;
	}
	
	//PUBLIC FUNCTION: getHasOne
	//Return the has one association
	public function getHasOne(){
		return $this->hasOne;
	}
	
	
	//PUBLIC FUNCTION: getManagementList
	//Return a list of the class
	public function getManagementList(){
		return $this->find( 'all' );	
	}
	
	//PUBLIC FUNCTION: getStructure
	//Returns the structure of the current model
	//In otherwords it'll dive deep down through all the associations and
	//return an array that might look something like...
	//
	// Array(
	//		['InitialModel'] => Array(
	//			['Fields'] => Array(
	//				'field1'
	//				'field2'
	//				...
	//			),
	//			['belongsTo'] => Array(
	//				...
	//			),
	//			['hasMany'] => Array(
	//				['AssociatedModel] => Array(
	//					['RelationshipData'] => Array(
	//						'foreignKey',
	//						'className'
	//					),
	//					['Fields'] => Array(
	//						'field1'
	//						'field2'
	//						...
	//					),
	//					['belongsTo'] => Array(
	//						...
	//					),
	//					['hasMany'] => Array(
	//						...
	//					),
	//					['hasOne'] => Array(
	//						...
	//					)
	//					...
	//				)
	//			...
	//			),
	//			['hasOne'] => Array(
	//				...
	//			)
	//		)
	//	)	
	public function getStructure( $modelName=null, $parentClass=null ){ 
		
		
		//Setup the model name if none was given
		if( $modelName == null ){
			$modelName = get_class( $this );
		}
			
		//Get the initial model's details
		$currentModelInstance 	= ClassRegistry::init( $modelName );
		$currentModel 			= $currentModelInstance->find('first');
		
		//On the off chance we're dealing with a model that doesn't have
		//any entries in the database for it, then we simply are going to
		//skip down and return an empty array
		if( count( $currentModel ) > 0 ){
			
			//Now that we have a find from the initial model we need
			//to tidy it up
			$currentModelFieldsArray = array();
			
			//Loop through what we found on the find('first') and throw these
			//fields onto the fields array
			foreach( $currentModel[ $modelName ] as $fieldName => $fieldValue ){
			
				//Tack on the field name
				$currentModelFieldsArray[] = $fieldName;	
				
			}
							
			//Now get all of the Associations of each type
			$belongsTo 	= $currentModelInstance->getBelongsTo();
			$hasMany	= $currentModelInstance->getHasMany();
			$hasOne		= $currentModelInstance->getHasOne();
			
			
			//There's a chance that doing this infinite loop checking might mean we
			//don't grab an association for anything so we just toss these up here
			//so that they're initialized
			$belongsToArray = array();
			$hasManyArray = array();
			$hasOneArray = array();
			
			//We'll start of with the belongsTo list since it comes
			//first alphabetically
			foreach( $belongsTo as $associatedModelName => $associatedModelArray ){
					
				//Get the structure for the belongsTo association
				$belongsToArray = $this->mergeAssociatedStructure( 
												$belongsToArray, 
												$associatedModelName, 
												$associatedModelArray, 
												$parentClass, 
												$modelName 
												);
				
			}

			//The following section has been removed due to loop complications. Was only ever
			//intended as a way to manage the many to many relationships, so instead at some
			//point in the future a way of managing those will be introduced as well as a way
			//to flag the Models based off junction tables as such so that they and they alone are
			//included.

			//Perhaps there will be a way to include these other associations as links to their
			//models management pages.

			/*/Setup the hasMany list so that we're getting away
			//from all of the extra fields and values we don't need
			foreach( $hasMany as $associatedModelName => $associatedModelArray ){

				//Get the structure data for the given associaton
				$hasManyArray = $this->mergeAssociatedStructure(
												$hasManyArray,
												$associatedModelName,
												$associatedModelArray,
												$parentClass,
												$modelName
												);

			}*/

			/*/Go again with the hasOne relationships like we did for hasMany
			foreach( $hasOne as $associatedModelName => $associatedModelArray ){

				//Get the structure data for the given association
				$hasOneArray = $this->mergeAssociatedStructure(
												$hasOneArray,
												$associatedModelName,
												$associatedModelArray,
												$parentClass,
												$modelName
												);

			}*/
			
			//Now that we've got all our data it's time to setup and return the final array
			$finalStructure = array(
								$modelName => array(
									'Fields' 	=> $currentModelFieldsArray,
									'belongsTo'	=> $belongsToArray,
									'hasMany'	=> $hasManyArray,
									'hasOne'	=> $hasOneArray
								)
							);
							
		}else{
			$finalStructure = array();
		}
			
		//Phew, we're done, return this mess			
		return $finalStructure;
									
		
	}
	
	//PUBLIC FUNCTION: getUIDList
	//Thought it might be nice to give myself a way of grabbing all the 
	//uid values for a given model
	public function getUIDList(){
		
		return $this->find( 'list', array(
							'fields' => get_class($this) . '.uid'		
						));
		
	}
	
	//PUBLIC FUNCTION: mergeAssociatedStructure
	//Get the structure for a given associated model and merge
	//it into the given array
	public function mergeAssociatedStructure( $array, $modelName, $modelArray, $grandParentClass, $parentClass ){
			
		//Setup the array, grab the relationship data, initialize the class and 
		//then call this function to get its structure.
		$relationshipData = $this->getAssociationArray( $modelName, $modelArray );
				
		//Make sure we're not bouncing backwards
		if( $relationshipData['className'] != $grandParentClass and $relationshipData['className'] != $parentClass ){		
		
			$associatedModelInstance	= ClassRegistry::init( $relationshipData['className'] );
			$associatedModelStructure	= $associatedModelInstance->getStructure( $modelName, $parentClass );
					
			//Add this structure to our hasMany array
			return array_merge_recursive( $array, $associatedModelStructure );
		
		//If we would've been bouncing backwards, just return the given array
		}else{
			return $array;
		}
	
	}
	
	//PUBLIC FUNCTION: remove
	//Remove the record with the given UID
	public function remove( $uid, $cascade=true ){
			
		$this->delete( $uid, $cascade );
		
	}
        
	//PUBLIC FUNCTION: saveWithJSONFormData
	//Save to the database using JSON values
	public function saveWithJSONFormData( $jsonValues = array() ){

			//Before we actually save anything we want to fix any potentially
			//erroneous boolean values.
			$jsonValues = $this->fixJSONValueBooleans( $jsonValues );

			//Do the actual saving		
			if( isset( $jsonValues['uid'] ) ){

				$this->read( null, $jsonValues['uid'] );
				$this->set( $jsonValues );
				return $this->save();
				
			}else{
				
				return 'noUIDSet';
				
			}
		
	}
	
}
