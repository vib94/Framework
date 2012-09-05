<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Int
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Int extends Yab_Validator_Abstract {

	const NOT_INT = 'Value must be an integer';

	public function _validate($value) {

		if((string) $value != (string) intval($value))
			$this->addError('NOT_INT', self::NOT_INT);

	}

}

// Do not clause PHP tags unless it is really necessary