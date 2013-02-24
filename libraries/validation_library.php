<?php

class validation_library extends app_library {
	public function run($validation_array, $post = FALSE, $human_readable_form = TRUE){
		/*
		 * example array:
		 * 
		 *	array(
		 *		"username" => array(
		 *			"maxlength" => 10,
		 *			"minlength" => 2,
		 *			"allowedcharacters" => "alpha,digits,space",
		 *			"trim" => true,
		 *			"datatype" => "integer",
		 *			"maximum" => 100,
		 *			"minimum" => -100
		 *		),
		 *		"password" => array(
		 *			"minlength" => 6,
		 *			"datatype" => "text",
		 *			"match" => "pwd_confirm"
		 *		)
		 *	)
		 * spec: 
		 *  trim = TRUE
		 *  match = 'confirm_field'
		 *  maxlength = n
		 *  minlength = n
		 *  maximum = n
		 *  minimum = n
		 *  datatype = 'integer|float|text|multiline_text|email'
		 *  allowedcharacters = 'alpha,digits,space,underscore'
		 *  label = 'better_name_for_field'
		 *  unique = array ("table" => "table_name", "field" => "field_to_check_against")
		 * 
		 * returned array:
		 * 
		 * if second argument (human_readable_form) is TRUE, it will return array of fields as keys with human readable messages as values like
		 *    array("username" => "Username already exists.", "password" => "Password should be longer than 6 characters.");
		 * otherwise it will return fields as keys and mismatched validation as values like
		 *    array("username" => "unique", "password" => "minlength")
		 */
		$failed_array = array();
		$request = $this->_controller->library("request");
		foreach($validation_array as $field => $field_validation_array){
			if($post === FALSE){
				$field_value = $request->post($field);
			} else {
				$field_value = $post[$field];
			}
			
			$field_label = isset($field_validation_array['label']) ? $field_validation_array['label'] : $field;
			
			if($field_value === FALSE){
				$failed_array[$field_label] = $human_readable_form ? "{$field_label} is not submitted." : "maxlength";
				continue;
			}
			
			$invalid_validation_array = array_diff(array_keys($field_validation_array), array("trim", "match", "maxlength", "minlength", "maximum", "minimum", "datatype", "allowedcharacters", "label", "unique"));
			if(count($invalid_validation_array) > 0){
				throw new Exception("Invalid validations: ".implode(", ", $invalid_validation_array));
			}
			// trim: trim before validation
			if(isset($field_validation_array['trim'])){
				if($field_validation_array['trim'] === TRUE){
					$field_value = trim($field_value);
				} else if($field_validation_array['trim'] !== FALSE){
					throw new Exception("Validation type: trim value is invalid.");
				}
			}
			
			// match: match with another field
			if(isset($field_validation_array['match'])){
				if($post === FALSE){
					$match_field_value = $request->post($field_validation_array['match']);
				} else {
					$match_field_value = $post[$field_validation_array['match']];
				}
				if($match_field_value !== $field_value){
					$failed_array[$field_label] = $human_readable_form ? "{$field_label} does not match." : "match";
					continue;
				}
			}
			
			// maxlength: length of maximum length for the value
			if(isset($field_validation_array['maxlength'])){
				if(is_numeric($field_validation_array['maxlength'])){
					$maxlength = (int)$field_validation_array['maxlength'];
					if(strlen($field_value) > $maxlength){
						$failed_array[$field_label] = $human_readable_form ? "{$field_label} should not be longer than {$maxlength} characters." : "maxlength";
						continue;
					}
				} else {
					throw new Exception("Validation type: maxlength value is invalid.");
				}
			}
			
			// minlength: length of minimum length for the value
			if(isset($field_validation_array['minlength'])){
				if(is_numeric($field_validation_array['minlength'])){
					$minlength = (int)$field_validation_array['minlength'];
					if(strlen($field_value) < $minlength){
						$failed_array[$field_label] = $human_readable_form ? "{$field_label} should not be shorter than {$minlength} characters." : "minlength";
						continue;
					}
				} else {
					throw new Exception("Validation type: maxlength value is invalid.");
				}
			}
			
			// maximum: maximum value for integer/float value
			if(isset($field_validation_array['maximum'])){
				if(is_numeric($field_validation_array['maximum'])){
					$maximum = (float)$field_validation_array['maximum'];
					if(is_numeric($field_value) === TRUE){
						$field_value_float = (float)$field_value;
						if($field_value_float > $maximum){
							$failed_array[$field_label] = $human_readable_form ? "{$field_label} should not be greater than {$maximum}." : "maximum";
							continue;
						}
					} else {
						array_push($failed_array, $field);
						continue;
					}
				} else {
					throw new Exception("Validation type: maximum value is invalid.");
				}
			}
			
			// minimum: minimum value for integer/float value
			if(isset($field_validation_array['minimum'])){
				if(is_numeric($field_validation_array['minimum'])){
					$minimum = (float)$field_validation_array['minimum'];
					if(is_numeric($field_value) === TRUE){
						$field_value_float = (float)$field_value;
						if($field_value_float < $minimum){
							array_push($failed_array, $field);
							continue;
						}
					} else {
						$failed_array[$field_label] = $human_readable_form ? "{$field_label} should not be less than {$minimum}." : "minimum";
						continue;
					}
				} else {
					throw new Exception("Validation type: minimum value is invalid.");
				}
			}
			
			// datatype: one of field types: integer, float, text, multiline_text, email
			if(isset($field_validation_array['datatype'])){
				$failed = FALSE;
				switch($field_validation_array['datatype']){
					case "integer": 
						if(!is_numeric($field_value)){
							$failed = TRUE;
						} else {
							$int_values = "0123456789";
							for($i = 0; $i < strlen($field_value); $i++){
								$character = substr($field_value, $i, 1);
								if(strpos($int_values, $character) === FALSE){
									$failed = TRUE;
									break;
								}
							}
						}
						break;
					case "float":
						if(!is_numeric($field_value)){
							$failed = TRUE;
						}
						break;
					case "text":
						if(strpos($field_value, "\n") !== FALSE || strpos($field_value, chr(0)) !== FALSE){
							$failed = TRUE;
						}
						break;
					case "multiline_text": // anything goes
						break;
					case "email":
						if(!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $field_value)){ // from codeigniter: lib/Form_validation.php:1045
							$failed = TRUE;
						}
						break;
					default:
						throw new Exception("Validation type: datatype value is invalid.");
				}
				if($failed === TRUE){
					$failed_array[$field_label] = $human_readable_form ? "{$field_label} is invalid." : "datatype";
					continue;
				}
			}
			
			// unique: array("table" => "some_table", "field" => "some_field")
			if(isset($field_validation_array['unique'])){
				$pdo = $this->_controller->library("database")->pdo();
				$table = $field_validation_array['unique']['table'];
				$table_field = $field_validation_array['unique']['field'];
				$pdo_statement = $pdo->prepare("SELECT {$table_field} FROM {$table} WHERE {$table_field} = :{$table_field}");
				$pdo_statement->bindParam(":{$table_field}", $field_value);
				$pdo_statement->execute();
				if($pdo_statement->rowCount() > 0){
					$failed_array[$field_label] = $human_readable_form ? "{$field_label} already exists." : "unique";
					continue;
				}
			}
			
			// allowedcharacters: separate these by comma: alpha, digits, space, underscore
			// make sure this is the last case because of shitty code
			if(isset($field_validation_array['allowedcharacters'])){
				$allowed_characters = array_unique(explode(",", $field_validation_array['allowedcharacters']));
				$allowed_characters_string = "";
				foreach($allowed_characters as $allowed_character_type){
					switch($allowed_character_type){
						case "alpha":
							for($i = ord('a'); $i <= ord('z'); $i++){
								$allowed_characters_string .= chr($i).strtoupper(chr($i));
							}
							break;
						case "digits":
							for($i = ord('0'); $i <= ord('9'); $i++){
								$allowed_characters_string .= chr($i);
							}
							break;
						case "space":
							$allowed_characters_string .= " \t\n";
							break;
						case "underscore":
							$allowed_characters_string .= "_";
							break;
						default:
							throw new Exception("Validation type: allowedcharacters value is invalid.");
					}
				}
				for($i = 0; $i < strlen($field_value); $i++){
					$character = substr($field_value, $i, 1);
					if(strpos($allowed_characters_string, $character) === FALSE){
						$failed_array[$field_label] = $human_readable_form ? "{$field_label} contains invalid characters." : "allowedcharacters";
						break;
					}
				}
			}
		}
		return $failed_array;
	}
}