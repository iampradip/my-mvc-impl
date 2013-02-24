<?php

require_once "core/router.php";
require_once "core/url.php";

// TODO prevent csrf

router::route(
	new url(
		array(
			"index_controller" => "index",  						// homepage controller
			"404_controller" => "error404", 						// arguments passed to index action: $controller, $action[, $argument1, $argument2, ..., $argumentn]
			"base_url" => dirname($_SERVER['SCRIPT_NAME']) 	// blank if on root folder
		)
	)
);