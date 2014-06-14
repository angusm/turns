<?php

//Handles requests for friendly column names
class FriendlyColumnNamesController extends AppController{

    //Handles the things that need to happen before an action is executed
    public function beforeFilter(){
        parent::beforeFilter();

        //Determine what's allowed and what isn't
        $this->Auth->allow();
    }

    //FUNCTIONS

    //PUBLIC FUNCTION: getFriendlyName
    //This function takes a model name and a column name and returns the friendly column name
    //for that combination. Pretty much just calls the model function
    public function getFriendlyName( $modelName, $columnName ){

        $friendlyName = $this->FriendlyName->getFriendlyName( $modelName, $columnName );

        $this->set( 'friendlyName', $friendlyName );
        $this->set('_serialize', array(
            'friendlyName'
        ));

    }

}