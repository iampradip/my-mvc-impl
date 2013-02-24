<?php

class model {
	protected $_controller;
	public function __construct($controller){
		$this->_controller = $controller;
	}
	public function library($library_name){
		return $this->_controller->library($library_name);
	}
}