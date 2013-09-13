<?php

//REQUIRED VARIABLES
//	headerName
//	disclosureName
//	hideType

//Give default values if none are defined
if( ! isset( $hideType ) ){
	$hideType = 'showMore';
}
if( ! isset( $disclosureName ) ){
	$disclosureName = 'null';
}
if( ! isset( $headerName ) ){
	$headerName = 'Expand/Contract';
}
if( ! isset( $neverShow ) ){
	$neverShow = 'null';
}

echo '<div '.	'class="disclosureToggle smallDisclosureToggle"'.
		'disclosureName="'.$disclosureName.'">';
		
		if( isset( $hideHeader ) and $hideHeader ){
			echo '<div class="disclosureDiv" disclosureName="'.$disclosureName.'">';
		}		
		echo '<h3 class="disclosureHeader">'.$headerName.'</h3>';
		if( isset( $hideHeader ) and $hideHeader ){
			echo '</div>';
		}
		echo $this->element('Disclosure/showArrows', array( 'disclosureName' => $disclosureName, 'hideType' => $hideType, 'neverShow' => $neverShow ) );
echo '</div>';	
echo '</td>';

?>		