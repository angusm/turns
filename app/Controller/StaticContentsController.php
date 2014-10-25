<?php

/**
 * Class StaticContentsController
 * @property mixed $params
 * @property StaticContent $StaticContent
 * @property mixed data
 */
class StaticContentsController extends AppController{

    //FUNCTIONS

    //PUBLIC FUNCTION: edit
    //Edit given content using a TinyMCE instance
	public function edit(){

        //Establish a default
        $staticContent  = '';
        $uid            = null;

        if( isset($this->params->query['uid']) ){
            $uid                    = $this->params->query['uid'];
            $staticContentRecord    = $this->StaticContent->find('first', [
                'conditions' => [
                    'StaticContent.uid' => $uid
                ]
            ]);
            if( $staticContentRecord != false ){
                $staticContent = $staticContentRecord['StaticContent']['html'];
            }

        }

        $this->set( 'uid',              $uid );
        $this->set( 'staticContent',    $staticContent );
        $this->set( '_serialize',       [
            'uid',
            'staticContent'
        ]);

    }

    //PUBLIC FUNCTION: editList
    //Display a list of content to edit
    public function editList(){
        $UIDs = $this->StaticContent->find('all',[
            'fields'    => [
                'uid'
            ]
        ]);

        $this->set('UIDs',$UIDs);
    }

    //PUBLIC FUNCTION: setHTML
    //Change the HTML of a given record in the database to the passed values
    public function setHTML(){

        $html   = null;
        $uid    = null;

        //Grab the UID and the HTML
        if( isset($this->data['uid']) && isset($this->data['nuHTML']) ){
            $html   = $this->data['nuHTML'];
            $uid    = $this->data['uid'];
        }
        if( isset($this->params->query['nuHTML']) && isset($this->params->query['uid']) ){
            $html = $this->params->query['nuHTML'];
            $uid  = $this->params->query['uid'];
        }

        //If we have a valid UID and HTML run the change
        if( $html != null && $uid != null ){
            $this->StaticContent->read( null, $uid );
            $this->StaticContent->set( [
                'html' => $html
            ]);
            $this->StaticContent->save();

            $this->set( 'success', true );
        }else{
            $this->set( 'success', false );
        }

        $this->set( '_serialize', [
            'success'
        ]);

    }

    //PUBLIC FUNCTION: view
    //Display the content of the given UID
    public function view(){

        //Establish a default
        $staticContent = '';

        //Grab content if possible
        if( isset($this->params->query['uid']) ){
            $staticContentRecord = $this->StaticContent->find('first', [
                'conditions' => [
                    'StaticContent.uid' => $this->params->query['uid']
                ]
            ]);

            if( $staticContentRecord != false ){
                $staticContent = $staticContentRecord['StaticContent']['html'];
            }

        }

        $this->set( 'staticContent', $staticContent );
        $this->set( '_serialize', [
            'staticContent'
        ]);

    }

}