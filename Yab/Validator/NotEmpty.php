<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_NotEmpty
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_NotEmpty extends Yab_Validator_Abstract {

	const IS_EMPTY = 'Value can not be empty';

	public function _validate($value) {

		if(strlen((string) $value) < 1)
			$this->addError('IS_EMPTY', self::IS_EMPTY);
	      
	}

}

// Do not clause PHP tags unless it is really necessary