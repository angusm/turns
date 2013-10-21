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
	
	echo '<div class="teamUnits">';
	
	echo $this->ModelUI->tableFromFind( 
									array(
										array( 'Unit' => null )
									),
									array(
										'Unit Type'	=> 'unit_types_uid',
										'Name'		=> 'name',
										'Count'		=> 'quantity',
										'button'	=> 'extraContent'
									)
								);
								
	echo $this->Html->tag(
							'div',
							$this->GamePlay->renderBoard( array( 'width' => 8, 'height' => 2 ) ) .
							$this->GamePlay->renderUnits( '', array() ),
							array(
								'class' => 'gameBoardContainer'
							)
						);
	
	echo '</div>';
		
	//Toss up the extra libraries and setup the unit list
	echo $this->Html->tag(
					'script',
					'window.pageLibraries = new Array(
											new Array( "Unit", "manageUnits" ),
											new Array( "Game", "elements" )
										);
					window.Unit_manageUnits_availableUnitList = '. json_encode( $unitList ).';'
					);

?>