<?php
    
		echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js');
        	echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js');
		echo $this->Html->script('Core');
		echo $this->element('script_dump'); 
		echo '<script>' .
			'var getJSURL 		= "'. $this->Html->url(["controller" => "js", "action" => ""]) .'/";' .
			'var homeURL 		= "'. $this->webroot .'";' .
			'var imgURL 		= "'. $this->Html->url(["controller" => "img", "action" => ""]) . '/";' .
			'var boardTileSize 	= 70;'.
		'</script>';
		
