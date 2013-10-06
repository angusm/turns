<input type="button" class="joinQueueButton" value="Play Now">

<?php

	
	//Toss up the extra libraries
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "Matchmaking", "joinQueue" )
										);'
					);

?>