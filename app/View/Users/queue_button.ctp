<div class="matchmakingButton">

	<?php
	
		echo $this->TurnForm->modelSelect( 
										$teams,
										'name',
										array(
											'class' => 'queueTeamSelect'
										)
									);
	
	?>

	<input type="button" class="joinQueueButton" value="Play Now">
	<div class="matchmakingTimeWaited"></div>
</div>

<?php

	
	//Toss up the extra libraries
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "Matchmaking", "joinQueue" )
										);'
					);

?>