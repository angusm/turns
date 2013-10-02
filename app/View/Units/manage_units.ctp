<?php

	//Setup the div that will contain all of the units the user has available to
	//put into their teams.
	echo '<div class="unitPool">';
	
	echo $this->ModelUI->tableFromFind( 
									$unitList,
									array(
										'Unit Type'	=> 'unit_types_uid',
										'Name'		=> 'name',
										'Count'		=> 'count',
										'button'	=> 'extraContent'
									),
									array(
										'button'	=> array(
														'tag'		=> 'input',
														'content'	=> '',
														'attributes'=> array(
																			'type'	=> 'button',
																			'value'	=> '>',
																			'class' => 'addUnitToTeamButton'
																		)
													)
									)
								);
	
	echo '</div>';
	
	echo $this->TurnForm->modelSelect( $teamList );
	
	echo '<div class="teamUnits"></div>';
	
	//Toss up the extra libraries
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "Unit", "manageUnits" )
										);'
					);

?>