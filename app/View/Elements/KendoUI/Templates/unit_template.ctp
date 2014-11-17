<?php

//This would be the template to display lists of available units
echo '<script type="text/x-kendo-template" id="unitTemplate">
		<tr>
			<td data-bind="text: unit_types_uid"></td>
			<td data-bind="text: name"></td>
			<td data-bind="text: quantity"></td>
			<td data-bind="text: teamcost"></td>
		</tr>
	</script>';