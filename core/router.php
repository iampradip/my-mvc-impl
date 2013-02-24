<?php

class router {
	public static function route($url){
		$controller_name_controller = "{$url->controller}_controller";
		
		// method should not start with "_"
		// controller should not start with "."
		// controller must be named by suffixing "_controller"
		if(strpos($url->action, "_") !== 0 && strpos($controller_name_controller, ".") !== 0 && file_exists("controllers/{$controller_name_controller}.php")){
			require_once "core/controller.php";
			require_once "controllers/app_controller.php";
			require_once "controllers/{$controller_name_controller}.php";
			// controller class name must be same as filename without extension
			// controller class name must extend "app_controller" class
			if(class_exists($controller_name_controller) && is_subclass_of($controller_name_controller, "app_controller") && method_exists($controller_name_controller, $url->action)){
				$controller = new $controller_name_controller($url);
				call_user_func_array(array($controller, $url->action), $url->arguments);
				return;
			}
		}
		
		// 404
		if($url->controller !== $url->route_config['404_controller']){
			return router::route(
				new url(
					$url->route_config, 
					$url->link($url->route_config['404_controller'], "index", array_merge(array($url->controller, $url->action), $url->arguments))
				)
			);
		} else {
			// if 404_controller doesn't exist,
			header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
			echo "<!doctype html><html><head><title>Error 404</title></head><body><h1>Object Not Found</h1><hr/><a href=\"".$url->base_link()."\">Homepage</a></body></html>";
			return;
		}
	}
}