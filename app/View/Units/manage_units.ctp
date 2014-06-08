<?php

	//Setup the div that will contain all of the units the user has available to
	//put into their teams.
	echo '<div class="unitPool">';
	
	echo $this->ModelUI->tableFromFind( 
									$unitList,
									array(
										'Unit Type'	=> 'unit_types_uid',
										'Name'		=> 'name',
										'Count'		=> 'quantity',
										'Cost'		=> 'teamcost',
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
													'includeNewButton' 		=> true,
													'includeRemoveButton'	=> true,
													'includeSaveButton' 	=> true
												)
											);

	echo $this->Html->tag(
							'div',
							0,
							array(
								'class' => 'TeamCost'
							)
						);
	
	echo $this->Html->tag(
							'div',
							'/' . $this->GamePlay->getMaxTeamCost(),
							array(
								'class' => 'MaxTeamCost'
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
										'Count'		=> 'quantity',
										'Cost'		=> 'teamcost'
									)
								);

	echo '</div>';
		
	echo $this->Html->tag(
						'div',
						$this->GamePlay->renderGameUnitCard(),
						array(
							'class' => 'manageUnitCardContainer'
						)
					);
		
	//Toss up the extra libraries and setup the unit list
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "Unit", "manageUnits" )
										);
					window.pageData = {
					    Game: {
					        uid: 26,
					        Board: {
					            height: 2,
					            width: 8
					        }
					    }
					};'
					);

?>