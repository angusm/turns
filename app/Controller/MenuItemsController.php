<?php

/**
 * Class MenuItemsController
 * @property MenuItem $MenuItem
 * @property AuthComponent $Auth
 */
class MenuItemsController extends AppController{

    //Setups anything that should happen before an action is executed
    public function beforeFilter(){
        parent::beforeFilter();
        //Allow the various actions that should be able to be accessed
        //even when a user is not logged in
        $this->Auth->allow();
    }

    //PUBLIC FUNCTION: getMenuItems
    //Return the menu items available to the current user
    public function getAvailableMenuItems(){

        $menuItems = $this->MenuItem->getAvailableMenuItems( $this->Auth->user['uid'] );

        $this->set( 'menuItems', $menuItems );
        $this->set( '_serialize', [
            'menuItems'
        ]);

    }

}