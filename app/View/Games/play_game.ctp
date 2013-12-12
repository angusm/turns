<?php

	echo $this->GamePlay->renderGame( $userUID, $gameInformation );
	echo $this->GamePlay->renderGameUnitCard();
	
	//Toss up the extra libraries
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "Game", "gameplay" )
										);'
					);

?>