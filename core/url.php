<?php

class url {
	private $_controller;
	private $_action;
	private $_arguments;
	private $_route_config;

	public function __construct($route_config_array, $path_info = FALSE){
		$this->_route_config = $route_config_array;
		
		if($path_info === FALSE){
			if(isset($_SERVER['PATH_INFO'])){
				$path_info = $_SERVER['PATH_INFO'];
			} else {
				$path_info = "";
			}
		}
		
		$url = explode("/", $path_info);
		if(!empty($url[1])){
			$this->_controller = $url[1];
		} else {
			$this->_controller = $this->_route_config['index_controller'];
		}
		
		if(!empty($url[2])){
			$this->_action = $url[2];
		} else {
			$this->_action = "index";
		}
		
		if(!empty($url[3])){
			$this->_arguments = array_slice($url, 3);
			if($this->_arguments[count($this->_arguments) - 1] === "")
				array_pop($this->_arguments); // remove blank argument for urls like /index.php/product/view/5/ -> array("5", "") -> array("5")
		} else {
			$this->_arguments = array();
		}
	}
	public function __get($name){
		switch($name){
			case "controller": 		return $this->_controller;
			case "action": 			return $this->_action;
			case "arguments": 		return $this->_arguments;
			case "route_config": 		return $this->_route_config;
		}
		throw new Exception("Undefined property: url::${$name}");
	}
	
	// returns link to given controller. use in <a href>
	public function base_link($controller_name = FALSE, $action_name = "index", $arguments = array(), $should_be_null = NULL){
		return $this->_route_config['base_url']."/index.php".$this->link($controller_name, $action_name, $arguments, $should_be_null);
	}
	public function link($controller_name = FALSE, $action_name = "index", $arguments = array(), $should_be_null = NULL){
		if($should_be_null !== NULL){
			throw new Exception("Invalid arguments for link() function.");
		}
		if($controller_name === FALSE){
			$controller_name = $this->_route_config['index_controller'];
		}
		
		$url = "";
		
		if(empty($arguments)){
			if($action_name === "index"){
				if($controller_name === $this->_route_config['index_controller']){
					$url = "";
				} else {
					$url = "/{$controller_name}";
				}
			} else {
				$url = "/{$controller_name}/{$action_name}";
			}
		} else {
			$args = $arguments;
			if(!is_array($args)){
				$args = array($args);
			}
			foreach($args as $key => $value){
				$args[$key] = trim($value, "/");
			}
			$url = "/{$controller_name}/{$action_name}/".implode("/", $args);
		}
		$url = rtrim($url, "/");
		return $url;
	}
	public function redirect($url, $should_be_null = NULL){
		if($should_be_null !== NULL){
			throw new Exception("Invalid arguments for redirect() function.");
		}
		header("Location: {$url}");
		exit;
	}
}