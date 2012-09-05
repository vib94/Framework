<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Equal
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Equal extends Yab_Validator_Abstract {

	const NOT_EQUAL = '"$1" is not equal to "$2"';

	public function _validate($value) {

		if(!in_array($value, $this->get('to', 'Array'), $this->has('strict')))
			return $this->addError('NOT_EQUAL', self::NOT_EQUAL, $value, implode(', ', $this->get('to', 'Array')));

	}	

}

// Do not clause PHP tags unless it is really necessary