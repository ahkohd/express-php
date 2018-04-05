<?php

/**
 * Module class definition
 */
class ExpressValidator {

	/**
	 * Validate a given data against a type
	 * @param string $type
	 * @param string $value
	 * @return boolean
	 */
	public function validate($type, $value) {
		switch(strtolower($type)) {
			case 'email':
				if (!filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
				    return true;
				} else {
				    return false;
				}
				break;
			case 'url':
				if (!filter_var($value, FILTER_VALIDATE_URL) === false) {
				    return true;
				} else {
				    return false;
				}
				break;
			case 'integer':
				if (filter_var($value, FILTER_VALIDATE_INT) === 0 || !filter_var($value, FILTER_VALIDATE_INT) === false) {
				    return true;
				} else {
				    return false;
				}
				break;
			case 'ip':
				if (!filter_var($value, FILTER_VALIDATE_IP) === false) {
				    return true;
				} else {
				    return false;
				}
				break;
		}
	}

	/**
	 * Sanitize a given value against a given criteria
	 * @param string $type
	 * @param string $value
	 * @return string
	 */
	public function sanitaize($type, $value) {
		switch($type) {
			case 'email':
				return filter_var($value, FILTER_SANITIZE_EMAIL);
				break;
			case 'string':
				return filter_var($value, FILTER_SANITIZE_STRING);
				break;
			case 'url':
				return filter_var($value, FILTER_SANITIZE_URL);
				break;
			case 'special char':
			 	return filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
			 	break;
			 case 'integer':
			 	return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
			 	break;
			 case 'float':
			 	return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
			 	break;

		}
	}

	/**
	 * Test a value against a given Regex
	 * @param string $regex
	 * @param string $value
	 * @return boolean
	 */
	public function test($regex, $value) {
		if (preg_match($regex, $value)) {
		  return true; 
		} else {
			return false;
		}
	}
}
