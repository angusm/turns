<?php
/**
 * Created by PhpStorm.
 * User: a.macdonald
 * Date: 09/06/14
 * Time: 1:08 PM
 */

//The Header Slides controller will handle requests relating to the slideshow
//set up at the top of every page
class HeaderSlidesController extends AppController{

    //Setups anything that should happen before an action is executed
    public function beforeFilter(){
        parent::beforeFilter();
        //Allow the various actions that should be able to be accessed
        //even when a user is not logged in
        $this->Auth->allow();
    }

    //PUBLIC FUNCTION: getAvailableSlideUIDs
    //Grab and return a 1D array of the UIDs of all the slides that are available to the user
    public function  getAvailableSlideUIDs(){

        $slideUIDs = $this->HeaderSlide->getAvailableSlideUIDs();

        $this->set( 'slideUIDs', $slideUIDs );
        $this->set( '_serialize', [
            'slideUIDs'
        ]);

    }

    //PUBLIC FUNCTION: getSlideData
    //Grab and return the information for a slide that is necessary to
    //display it. Only doing so if the slide would be available to the user
    public function getSlideData(){

        //Grab the jSON request variables from the parameters
        $json = $this->params['url'];

        //If there's no valid UID provided then return false
        if( isset( $json['uid'] ) ){
            //Make the request to the model
            $slideData = $this->HeaderSlide->getSlideData(
                $json['uid']
            );
        }else{
            $slideData = false;
        }

        //Set the variables we'll be returning
        $this->set( 'slideData', $slideData );
        $this->set( '_serialize', [
            'slideData'
        ]);


    }

}