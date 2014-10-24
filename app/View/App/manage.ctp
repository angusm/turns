<?php

	/*/Selection list and new unit type button
	echo $this->TurnForm->modelSelect( $managementList );
	echo $this->TurnForm->newRecordButton( $modelName );
	echo $this->TurnForm->saveRecordButton( $modelName );*/
	
	//List the fields for the initial model
	echo $this->TurnForm->fullModelSetupForm( $structure );
        
	//Toss up the extra libraries
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "TurnForm", "manage" )
										);'
					);

