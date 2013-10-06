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
	
	echo $this->TurnForm->editableModelSelect( 
												$teamList, 
												array(
													'includeNewButton' 	=> true,
													'includeSaveButton' => true
												)
											);
	
	echo '<div class="teamUnits">';
	
	echo $this->ModelUI->tableFromFind( 
									array(
										array( 'Unit' => null )
									),
									array(
										'Unit Type'	=> 'unit_types_uid',
										'Name'		=> 'name',
										'Count'		=> 'count',
										'button'	=> 'extraContent'
									)
								);
	
	echo '</div>';
	
	//Toss up the extra libraries
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "Unit", "manageUnits" )
										);'
					);

?>