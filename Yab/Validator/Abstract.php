<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Abstract
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

abstract class Yab_Validator_Abstract extends Yab_Object {
	
	protected $_errors = array();
	protected $_override_errors = array();

	final public function validate($value, array $override_errors = array()) {

		$this->_errors = array();
		$this->_override_errors = $override_errors;

		$this->_validate($value);

		return ((bool) (count($this->_errors) === 0));

	}

	final public function addError() {

		$args = func_get_args();

		$error_code = array_shift($args);
		$error_message = array_shift($args);

		if(array_key_exists($error_code, $this->_override_errors))
			$error_message = $this->_override_errors[$error_code];

		foreach($args as $key => $value)
			$error_message = str_replace('$'.($key + 1), $value, $error_message);

		$this->_errors[$error_code] = $error_message;

		return $this;

	}

	final public function getErrors() {

		return $this->_errors;

	}

	abstract public function _validate($value);

}

// Do not clause PHP tags unless it is really necessary