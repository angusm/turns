<?php

class DatabasesController extends AppController{

    //Handles the things that must happen before an action is executed
    public function beforeFilter(){
        //Respect those who came before
        parent::beforeFilter();

        //Make a list of what unauthenticated people can authorize.
        $this->Auth->allow();

    }

    //Get the database list
    public function getList(){

        //Get a list of all the controllers, since each controller
        //is set up through the AppController to allow for management
        //we know that we can edit any database table with a controller
        $databaseList = App::objects( 'controller' );

        //But we don't want to be worrying about this controller or the
        //app controller so we take those off.
        foreach( $databaseList as $databaseIndex => $controllerName ){

            if(
                $controllerName == 'AppController' ||
                $controllerName == 'DatabasesController'
            ){
                unset( $databaseList[$databaseIndex] );
            }

        }

        //Pass the database list to the view and serialize it
        $this->set( 'databaseList', $databaseList );
        $this->set( '_serialize', array(
            'databaseList'
        ));


    }

}

?>