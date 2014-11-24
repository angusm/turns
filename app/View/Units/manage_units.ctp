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

//Toss out the information for the team units and the unit pool
echo $this->Html->tag(
	'script',
	'require(
		[
			'.$this->RequireJS->requireJSFromLib('/Utilities/functions.js').',
			'.$this->RequireJS->requireJSFromLib('/Unit/manageUnits.js').',
		],
		function(){
			window.Units    = defaultValue(window.Units, {});
			window.Teams    = defaultValue(window.Teams, {});
			window.Units.UnitPool = '.json_encode($unitList).';
			window.Teams.TeamList = '.json_encode($teamList).';
		}
	);'
);