<?php
// app/Controller/UsersController.php
class UnitStatsController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
    }

    //PUBLIC FUNCTION: manage
    //Setup a screen to manage the data
    public function manage(){

        //Pass forward the structure, management list and model name
        $this->set(
            'structure',
            $this->UnitStat->getStructure(
                [],
                [],
                ['GameUnitType', 'GameUnit', 'Unit', 'GameUnitStat', 'UnitType']
            )
        );

        //Render the view
        $this->render('../App/manage');

    }

}