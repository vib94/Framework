<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Options
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Options extends Yab_Validator_Abstract {

	const NOT_VALID  = '"$1" is not a valid option';
	const NOT_ENOUGH  = 'not enough options selected';
	const TOO_MANY  = 'too many options selected';

	public function _validate($value) {

		$filter_array = new Yab_Filter_Array();

		$value = $filter_array->filter($value);

		if($this->has('min_options') && count($value) < $this->get('min_options')) 
			$this->addError('NOT_ENOUGH', self::NOT_ENOUGH, count($value), $this->get('min_options'));

		if($this->has('max_options') && $this->get('max_options') < count($value)) 
			$this->addError('TOO_MANY', self::TOO_MANY, count($value), $this->get('max_options'));

		foreach($value as $v) {

			if(!$this->_validOption($this->get('options'), $v))
				$this->addError('NOT_VALID', self::NOT_VALID, $v);

		}

	}

	private function _validOption($options, $value) {

		foreach($options as $key => $option) {

			if(!is_array($option) && $value == $key)
				return true;

			if(!is_array($option)) 
				continue;

			foreach($option as $k => $o) 
				if(!is_array($o) && $k == $value)
					return true;

		}  

		return false;

	}

}

// Do not clause PHP tags unless it is really necessary