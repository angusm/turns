<?php
	echo $this->Html->tag(
		'script',
		'',
		array(
			'data-main' => $this->Html->url(["controller" => "js", "action" => ""]) .'/Core',
			'src'       => $this->Html->url(["controller" => "js", "action" => ""]) .'/require.js',
		)
	);
		
