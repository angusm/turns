<?php

	$baseClasses = 'disclosureArrow independentDisclosure';

	//Default neverShow
	if( ! isset( $neverShow ) ){
		$neverShow = 'null';
	}
	//Default hideType
	if( ! isset( $hideType ) ){
		$hideType = 'null';
	}
	
	if( $neverShow != 'showLess' ){
	
		$classes = $baseClasses.' showLess';
		if( $hideType == 'showLess' ){
			$classes .= ' hidden';
		}
	
		//$hideType should be 'showMore' or 'showLess' depending on what we want to hide
		echo $this->Html->image('menu_images/downPointingArrow.JPG', array(
									'alt' 			=> 'Show Less', 
									'class' 		=> $classes,
									'disclosureName' 	=> $disclosureName
								));
	}
	if( $neverShow != 'showMore' ){
	
		$classes = $baseClasses.' showMore';
		if( $hideType == 'showMore' ){
			$classes .= ' hidden';
		}
		
		echo $this->Html->image('menu_images/rightPointingArrow.JPG', array(
									'alt' 			=> 'Show More', 
									'class' 		=> $classes,
									'disclosureName' 	=> $disclosureName
								));
	}
	
?>