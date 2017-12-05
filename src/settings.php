<?php

return [
	'settings' => [
		'displayErrorDetails' => false, // set to false in production
	        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

		'salt' => '***ENTER SALT THERE***',
		'rpc' => '/usr/bin/curl -X POST http://localhost:18082/json_rpc -d',

		'webWalletsDir' => '/opt/fonero/wallets/',

		// Renderer settings
	        'renderer' => [
	        	'template_path' => __DIR__ . '/../templates/',
        	],
        ]
];
