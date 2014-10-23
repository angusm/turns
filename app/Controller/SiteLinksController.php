<?php

class SiteLinksController extends AppController{

    //Called before anything else is processed to determine which pages
    //an unathenticated user is allowed to access
    public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow( 'getContent' );
    }

    //FUNCTIONS

    //PUBLIC FUNCTION: getContent
    //Returns the content that a given link would provide if followed, taking
    //in any optional parameters
    public function getContent(){

        //Initialize the parameters array
        $parameters = [];

        //Initialize the parameter set model
        $parameterSetModel  = ClassRegistry::init('ParameterSet');

        //Grab the site link by the passed UID
        $siteLink = $this->SiteLink->find( 'first', [
            'conditions' => [
                'SiteLink.uid' => $this->params->query['site_links_uid']
            ]
        ]);

        //Grab any parameters from a passed parameter_sets_uid, if applicable
        if( isset($this->params->query['parameter_sets_uid']) ){
            $parameterRecords    = $parameterSetModel->find( 'all', [
                'conditions' => [
                    'ParameterSet.uid' => $this->params->query['parameter_sets_uid']
                ]
            ]);


            //Setup the parameters for the url
            foreach( $parameterRecords as $key => $parameter ){
                $parameters[$parameter['key']] = $parameter['value'];
            }
        }

        //If the parameters request type is set then we follow up on it
        if(
            isset($this->params->query['requestType']) &&
            $this->params->query['requestType'] == 'content' &&
            $siteLink['SiteLink']['controller'] != 'css' &&
            $siteLink['SiteLink']['controller'] != 'files' &&
            $siteLink['SiteLink']['controller'] != 'img' &&
            $siteLink['SiteLink']['controller'] != 'js'
        ){
            $parameters['requestType'] = 'content';
        }


        $redirectURL = [
            'controller'    => $siteLink['SiteLink']['controller'],
            'action'        => $siteLink['SiteLink']['action'],
            '?'             => $parameters
        ];

        if( $redirectURL['controller'] == '' ){
            $redirectURL = '/';
            if(
                isset($parameters['requestType']) &&
                $parameters['requestType'] == 'content'
            ){
                $redirectURL .= '?requestType=content';
            }
        }

        //Run the redirect
        $this->redirect($redirectURL);

    }

}