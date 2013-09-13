
<div class="unitCard">
    <div class="cardArtLayers">    
		<?php
			//Loop through the card art layers and display them
			foreach( $cardArtLayerSets as $cardArtLayerSet ){
				echo $this->Html->image( 	$cardArtLayerSet['CardArtLayer']['image'], 
											array(
												'alt' => 
													'Card Art Layer',
												'cardArtLayerPosition' => 
													$cardArtLayerSet['position']
											)
										);
			}            
        ?>
    </div>
    
    <div class="boardIcon">
		<?php
			echo $this->Html->image( $boardIcon, array( 'alt' => 'Board Icon'));
		?>    
    </div>
    
    <div class="attackBar attributeBar">
    	<?php		
						
			//For every defense value point, display a defense icon
			for( $attackCounter = 0; $attackCounter < $attackValue; $attackCounter++ ){
				echo $this->Html->image( $attackPointIcon, array(
															'alt' 	=> 'Attack Point',
													  		'class'	=> 'attributePoint'
														));	
			}
			
		?>
        <div class="attackValue attributeValue">
			<?php
                echo $attackValue;			
            ?>
        </div>
    </div>
    <div class="attackIcon attributeIcon">
		<?php
			echo $this->Html->image( $attackIcon, array(
													  'alt' 	=> 'Attack Icon'
												  ));	
		?>    
    </div>
    
    <div class="defenseBar attributeBar">
    	<?php		
						
			//For every defense value point, display a defense icon
			for( $defenseCounter = 0; $defenseCounter < $defenseValue; $defenseCounter++ ){
				echo $this->Html->image( $defensePointIcon, array(
															'alt' 	=> 'Defense Point',
													  		'class'	=> 'attributePoint'
														));	
			}
			
		?>
        <div class="defenseValue attributeValue">
        	<?php
				echo $defenseValue;
			?>
        </div>
    </div>    
    <div class="defenseIcon attributeIcon">
		<?php
			echo $this->Html->image( $defenseIcon, array(
													  'alt' 	=> 'Defense Icon'
												  ));	
		?>    
    </div>
    
    <div class="teamcostBar attributeBar">
    	<?php		
						
			//For every defense value point, display a defense icon
			for( $teamcostCounter = 0; $teamcostCounter < $teamcostValue; $teamcostCounter++ ){
				echo $this->Html->image( $teamcostPointIcon, array(
															'alt' 	=> 'Team Cost Point',
													  		'class'	=> 'attributePoint'
														));	
			}
			
		?>
        <div class="teamcostValue attributeValue">
        	<?php
				echo $teamcostValue;			
			?>
        </div>
    </div>
    <div class="teamcostIcon attributeIcon">
		<?php
			echo $this->Html->image( $teamcostIcon, array(
													  'alt' 	=> 'Team Cost Icon'
												  ));	
		?>    
    </div>
    
    <div class="unitStatBox">
        
        <div class="unitTypeName">
            <?php
                echo $unitTypeName;
            ?>
        </div>

    </div>
    
    <div class="movementClasses">
    	<?php
			//We need to create and display all the possible movement sets a 
			//Unit Type might have
			foreach( $unitTypeMovementSets as $unitTypeMovementSet ){
				
				$movementSet = $unitTypeMovementSet['MovementSet'];
			
				//Setup the div to hold this movement set
				echo '<div 	class="movementSet" '.
							' movementSetUID="' . $movementSet['uid'] .
						'">';

					//Toss out the name
					echo '<div class="movementSetName">';
						echo $movementSet['name'];
					echo '</div>';
					//We have to feature the movement
					foreach( $movementSet['Movement'] as $movement ){
						
						//Setup the containing div, store the priority in an attribute
						//We don't need to show this explicitly as it should be clear to
						//the player through positioning
						echo '<div '.
								'class="movement" '.
								'priority="' . $movement['priority'] . '"' .
								'>';
														
							//Display the movement set background icon
							echo $this->Html->image( $movementBoxIcon, 
													array(
														'alt' 		=> 	'Movement Box',
														'class'		=>	'movementBox'
													));
							
							//Display the spaces that can be covered in this move
							echo '<div class="movementSpaces">';
								echo $movement['spaces'];
							echo '</div>';
							
							//Setup a display div for all the arrows
							echo '<div class="movementArrows">';
							foreach( $movement['MovementDirectionSet'] as $movementDirectionSet ){
								foreach( $movementDirectionSet['DirectionSet']['DirectionSetDirection'] as $directionSetDirection ){
									
									//Get a nicer variable name to work with
									$direction = $directionSetDirection['Direction'];
									
									//Toss up the image, storing its gameplay information in the
									//HTML
									echo $this->Html->image( $movementArrowIcon, 
															array(
																'alt' 		=> 	$direction['name'] . ' Movement Arrow',
																'x'			=>  $direction['x'],
																'y'			=> 	$direction['y'],
																'class'		=> 	'movementArrow' . $direction['name'],
																'direction'	=>	$direction['name']
															));
									
								}
							}
							echo '</div>';
							
						echo '</div>';
								
					}
					echo '</div>';		
			}
    	?>
    </div>
    <div class="movementSelectors">
    	<?php
			//For each movement set, toss out an icon that can be used to
			//select it
			foreach( $unitTypeMovementSets as $unitTypeMovementSet ){
				$movementSet = $unitTypeMovementSet['MovementSet'];
				echo '<div class="movementSetSelector" 
						movementSetUID="' . $movementSet['uid'] . 
					'">';
					
					echo $this->Html->image( $movementSetSelectorIcon, 
											array(
												'alt' 		=> 	'Movement Set Selector Icon',
												'class'		=>  'movementSetSelector'
											));
															
				echo '</div>';
			}
		?>
    </div>
</div>


<?php




?>