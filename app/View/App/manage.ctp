<?php

	/*/Selection list and new unit type button
	echo $this->TurnForm->modelSelect( $managementList );
	echo $this->TurnForm->newRecordButton( $modelName );
	echo $this->TurnForm->saveRecordButton( $modelName );*/
	
	//List the fields for the initial model
	echo $this->TurnForm->fullModelSetupForm( $structure );

