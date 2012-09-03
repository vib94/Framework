<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_LowerThan
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_LowerThan extends Yab_Validator_Abstract {

	const NOT_LOWER = 'Value is not lower than "$1"';
	const NOT_LOWER_OR_EQUAL = 'Value is not lower or equal to "$1"';

	public function _validate($value) {

		if($this->has('equal') && $this->get('than') <= $value)
			return $this->addError('NOT_LOWER_OR_EQUAL', self::NOT_LOWER_OR_EQUAL, $this->get('than'));

		if($this->get('than') < $value)
			return $this->addError('NOT_LOWER', self::NOT_LOWER, $this->get('than'));

	}

}

// Do not clause PHP tags unless it is really necessary