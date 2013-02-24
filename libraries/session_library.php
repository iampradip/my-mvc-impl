<?php

class session_library extends app_library {
	public function __construct($controller){
		parent::__construct($controller);
		session_name("session");
		session_start();
	}
	public function get($name, $default = FALSE){
		if(isset($_SESSION[$name]))
			return $_SESSION[$name];
		else
			return $default;
	}
	public function set($name, $value){
		$_SESSION[$name] = $value;
	}
	public function delete($name){
		unset($_SESSION[$name]);
	}
	public function remove(){
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(), '', 0);
		session_regenerate_id(true);
	}
}