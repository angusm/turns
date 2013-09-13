<?php

	//Selection list and new unit type button
	echo $this->TurnForm->modelSelect( $managementList );
	echo $this->TurnForm->newRecordButton( 'Unit Type' );
	echo $this->TurnForm->saveRecordButton( 'Unit Type' );
	
	//List of fields for each directly associated model

?>