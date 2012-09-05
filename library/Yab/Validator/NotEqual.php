<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_NotEqual
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_NotEqual extends Yab_Validator_Abstract {

	const IS_EQUAL = 'Value can not be equal to "$1"';

	public function _validate($value) {

		if(in_array($value, $this->get('to', 'Array'), $this->has('strict')))
			return $this->addError('IS_EQUAL', self::IS_EQUAL, implode(', ', $this->get('to', 'Array')));

	}

}

// Do not clause PHP tags unless it is really necessary