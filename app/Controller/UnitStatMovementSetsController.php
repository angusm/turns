<?php

class UnitStatMovementSetsController extends AppController{

    //The before filter will handle things before an action goes
    public function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allow();
    }

}