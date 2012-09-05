<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_NotNull
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_NotNull extends Yab_Validator_Abstract {

	const IS_NULL = 'Value can not be null';

	public function _validate($value) {

		if($value === null)
			$this->addError('IS_NULL', self::IS_NULL);

	}

}

// Do not clause PHP tags unless it is really necessary