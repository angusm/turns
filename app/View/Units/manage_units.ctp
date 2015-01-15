<?php

//Setup the unit pool
echo $this->Angular->dump('unitPool',$unitList);
echo $this->Angular->dump('teamList',$teamList);
echo '<table class="unitPool" ng-controller="UnitController as unitPool">
		<thead>
			<tr>
				<th>Unit Type</th>
				<th>Name</th>
				<th>Count</th>
				<th>Cost</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="unit in unitPool.units">
				<td>{{unit.type}}</td>
				<td>{{unit.name}}</td>
				<td>{{unit.count}}</td>
				<td>{{unit.cost}}</td>
				<td></td>
			</tr>
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