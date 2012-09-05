<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_GreaterThan
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_GreaterThan extends Yab_Validator_Abstract {

	const NOT_GREATER = 'Value is not greater than "$1"';
	const NOT_GREATER_OR_EQUAL = 'Value is not greater or equal to "$1"';

	public function _validate($value) {

		if($this->has('equal') && $value <= $this->get('than'))
			return $this->addError('NOT_GREATER_OR_EQUAL', self::NOT_GREATER_OR_EQUAL, $this->get('than'));

		if($value < $this->get('than'))
			return $this->addError('NOT_GREATER', self::NOT_GREATER, $this->get('than'));

	}

}

// Do not clause PHP tags unless it is really necessary