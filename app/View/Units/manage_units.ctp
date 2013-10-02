<?php

	//Setup the div that will contain all of the units the user has available to
	//put into their teams.
	echo '<div class="unitPool">';
	
	echo $this->ModelUI->tableFromFind( 
									$units,
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
																			'value'	=> '>'
																		)
													)
									)
								);
	
	echo '</div>';

?>