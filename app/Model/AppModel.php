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
	public static $alphaNumericMessage 	= 'This isn\'t a game for 14 year old girls on MSN messenger. Use real characters, letters and numbers.';
	public static $booleanMessage 		= 'Do or do not, there is no try. TRUE or FALSE.';
	public static $decimalMessage 		= 'Decimals only, bitch.';
	public static $numericMessage 		= 'Numbers only, bitch.';
		
	
	//Handy universal functions
	
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
//					$this->set( $fieldName, $fieldCriteria['default'] );
				}
				
			}
		}
		
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
		
		//Loop t hrough and create the 
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
		$currentModel 			= $currentModelInstance->find( );
		
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
	
	//PUBLIC FUNCTION: setupUIDRelation
	//Setup a foreign key relation between a group of models
	public function setupUIDRelation( $modelArray = array() ){
			
			//If the validate array doesn't exist, set it up
			if( isset( $this->validate ) ){
				
			}else{
				$this->validate = array();	
			}
			
			//Loop through every model we need to add
			foreach( $modelArray as $model1Name ){
				
				//Setup the models, we'll need a list of direction set IDs as well
				//as a list of movement IDs
				$model1	= ClassRegistry::init( $model1Name );
				
				//Now get the lists
				$model1UIDs	= $model1->getUIDList();
			
				//Add the current model to the validate array
				$this->validate = array_merge( 
											array(
													Inflector::underscore($model1Name) . 's_uid' => array(
															'rule'    => array('inList', $model1UIDs),
															'message' => 'Must be a valid ' . $model1Name . ' key',
															'required' 	=>	true
													)
											),
											$this->validate
									);
			}
			
	}
	
}
