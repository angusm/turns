<?php

//Setup the unit pool
echo $this->Knockout->dump('unitPool',$unitList);
echo $this->Knockout->dump('teamList',$teamList);
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

// Toss out the information for the team units and the unit pool
echo $this->Html->tag(
	'script',
	'requirejs(
		[
			"Utilities/functions",
			"Unit/manageUnits"
		]
	);',
	array(
		'type' => 'text/javascript'
	)
);