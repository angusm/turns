<?php
    
		echo $this->Html->script('JQuery');
		echo $this->Html->script('JQueryUI');
		echo $this->Html->script('JQueryUITouch');
		echo $this->Html->script('Core');
		echo $this->element('script_dump'); 
		echo '<script>' .
			'var getJSURL = "'. $this->Html->url(array("controller" => "js", "action" => "")) .'/";' .
			'var homeURL = "'. $this->webroot .'";' .
		'</script>';
		
?>