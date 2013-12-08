<?php
    
		echo $this->Html->script('JQuery');
		echo $this->Html->script('JQueryUI');
		echo $this->Html->script('JQueryUITouch');
		echo $this->Html->script('Libraries/Pixastic/pixastic.jquery');
		echo $this->Html->script('Libraries/Pixastic/pixastic.core');
		echo $this->Html->script('Libraries/Pixastic/actions/coloradjust');
		echo $this->Html->script('Core');
		echo $this->element('script_dump'); 
		echo '<script>' .
			'var getJSURL 		= "'. $this->Html->url(array("controller" => "js", "action" => "")) .'/";' .
			'var homeURL 		= "'. $this->webroot .'";' .
			'var imgURL 		= "'. $this->Html->url(array("controller" => "img", "action" => "")) . '/";' .
			'var boardTileSize 	= 70;'.
		'</script>';
		
?>