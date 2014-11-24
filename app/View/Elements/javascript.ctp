<?php

	echo $this->Html->tag(
		'script',
		"
		/**
		 * Store a bunch of paths we're going to want to use
		 */
		window.Paths = {};
		window.Paths.webroot = window
			.location
			.pathname
			.split('/')
			.slice(0,2)
			.join('/') + '/';
		window.Paths.jsDir      = window.Paths.webroot + 'js/';
		window.Paths.jsLibDir   = window.Paths.webroot + 'js/Libraries/';
		window.Paths.imgDir     = window.Paths.webroot + 'img/';
		"
	);

	echo $this->Html->tag(
		'script',
		'',
		array(
			'data-main' => $this->Html->url(["controller" => "js", "action" => ""]) .'/Core.js',
			'src'       => $this->Html->url(["controller" => "js", "action" => ""]) .'/require.js',
		)
	);
		
