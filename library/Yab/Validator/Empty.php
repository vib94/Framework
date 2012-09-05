<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Empty
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Empty extends Yab_Validator_Abstract {

	const NOT_EMPTY = 'Value must be empty';

	public function _validate($value) {

		if(0 < strlen((string) $value))
			$this->addError('NOT_EMPTY', self::NOT_EMPTY);
	      
	}

}

// Do not clause PHP tags unless it is really necessary