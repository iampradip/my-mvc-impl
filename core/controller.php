<?php

class controller {
	private $_models = array();
	private $_libraries = array();
	private $_url = array();
	public function __construct($url){
		$this->_url = $url;
	}
	public function url(){
		return $this->_url;
	}
	public function model($model_name){
		$model_name_model = "{$model_name}_model";
		if(!isset($this->_models[$model_name])){
			if(file_exists("models/{$model_name_model}.php")){
				require_once "core/model.php";
				require_once "models/app_model.php";
				require_once "models/{$model_name_model}.php";
				if(is_subclass_of($model_name_model, "app_model")){
					$this->_models[$model_name] = new $model_name_model($this);
				} else {
					throw new Exception("Class \"{$model_name_model}\" is not subclass of app_model class.");
				}
			} else {
				throw new Exception("Model \"{$model_name}\" doesn't exist.");
			}
		}
		return $this->_models[$model_name];
	}
	public function library($library_name){
		$library_name_library = "{$library_name}_library";
		if(!isset($this->_libraries[$library_name])){
			if(file_exists("libraries/{$library_name_library}.php")){
				require_once "core/library.php";
				require_once "libraries/app_library.php";
				require_once "libraries/{$library_name_library}.php";
				if(is_subclass_of($library_name_library, "app_library")){
					$this->_libraries[$library_name] = new $library_name_library($this);
				} else {
					throw new Exception("Class \"{$library_name_library}\" is not subclass of app_library class.");
				}
			} else {
				throw new Exception("Library \"{$library_name}\" doesn't exist.");
			}
		}
		return $this->_libraries[$library_name];
	}
	public function view($view_name, $view_variables = array()){
		if(file_exists("views/{$view_name}_view.php")){
			$controller = $this;
			$base_url = $this->url()->route_config['base_url'];
			extract($view_variables, EXTR_SKIP);
			require "views/{$view_name}_view.php";
		} else {
			throw new Exception("View \"{$view_name}\" doesn't exist.");
		}
	}
}