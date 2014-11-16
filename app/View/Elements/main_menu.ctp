
<?php

echo '<ul id="mainMenu" class="menu">';

//Loop through each menu item and toss out its element
//In order to display the menu properly nested we'll need to keep track of the left
//value of each element and indent when they're sequential and de-indent when they're not.
$leftValue      = -1;
$closuresNeeded = 0;
foreach( $menuItems as $menuItem ){

	//Determine if we need to close the previous elements
	if( $leftValue != -1 && intval($menuItem['MenuItem']['lft']) != $leftValue+1 ){
		echo '</ul></li>';
		$closuresNeeded--;
	}

	//Toss out the constant element
	echo '<li class="menuItem">';

	//Establish the

	echo $this->MenuItem->link($menuItem);
	echo '<ul class="menuItemChildren">';
	$closuresNeeded++;
	$leftValue  = intval($menuItem['MenuItem']['lft']);

}

//Close the last element on the menu
for( $counter = 0; $counter < $closuresNeeded; $counter++ ){
	echo '</ul></li>';
}

//Close the main menu
echo '</ul>';