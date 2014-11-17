<?php

//Setup the unit pool
echo '<table class="unitPool">
		<thead>
			<tr>
				<th>Unit Type</th>
				<th>Name</th>
				<th>Count</th>
				<th>Cost</th>
				<th></th>
			</tr>
		</thead>
		<tbody data-bind="source: unitPool" data-template="unitTemplate">
		</tbody>
	</table>';

//Toss out a script to define the template
echo $this->element('KendoUI/Templates/unit_template');

//Bring in the script for unit management
echo $this->Html->script('Libraries/Unit/manageUnits');

//Toss out the information for the unit
echo $this->KendoUI->exportResults($unitList);