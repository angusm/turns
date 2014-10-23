<?php

//Catalogue Category Relationship
//Parent class for all other catalogue category relationships

class CatalogueCategoryRelationship extends AppModel {

	//ASSOCIATIONS:
	public $belongsTo = [
			'CatalogueCategory' => [
				'className' => 'CatalogueCategory'
			]
		];
			
	//Make this model containable
	//This way we don't always have to get all the associated data
	public $actsAs = ['Containable'];
		
	public $relationshipColumnName = '';
	
	//FUNCTION: addRelationship
	//Add a relationship within this model to the given value
	//In all likelihood this should be overwritten by the child class
	//to provide better error and exception handling for when the user
	//hasn't provided perfect data.
	public function addRelationship( $categoryID, $nuRelationship ){
	
		//First make sure we don't have a relationship for this
		//already
		$exists = $this->find( 'first', [
			'conditions' => [
				get_class($this).'.catalogue_category_id' => $categoryID,
				get_class($this).'.'.$this->relationshipColumnName => $nuRelationship
			],
			'fields' => [
				get_class($this).'.id'
			]
		]);
		
		//If no such relationship already exists then
		//we create a new relationship and return its ID
		if( $exists == false ){
			
			$this->create();
			$this->set( 'catalogue_category_id',		$categoryID );
			$this->set( $this->relationshipColumnName,	$nuRelationship );
			$this->save();
			
			return $this->id;
	
		//Otherwise we return the existing relationship ID
		}else{
		
			return $exists[ get_class($this) ]['id'];
		
		}
	
	}
			
	//FUNCTION: getListForManagement
	//Returns a list of the various records associated with a given category
	public function getListForManagement( $categoryID, $fields = [] ){
	
		//In addition to any other fields, get the ID
		$fields[] = get_class($this).'.id';
	
		$managementList = $this->find( 'all', [
				'conditions' => [
					get_class($this).'.catalogue_category_id' => $categoryID
				],
				'fields' => $fields,
				'recursive' => 1
			]);
		
		return $managementList;
	}
	
	//FUNCTION: removeRelationship
	//Removes a given relationship from the category
	public function removeRelationship( $relationshipID ){
	
		$this->delete( $relationshipID, false );
	
	}

}