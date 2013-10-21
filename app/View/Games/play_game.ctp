<?php

	echo $this->GamePlay->renderGame( $userUID, $gameInformation );
	
	//Toss up the extra libraries
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "Game", "gameplay" )
										);'
					);

?>