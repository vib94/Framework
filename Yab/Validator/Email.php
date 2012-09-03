<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Email
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Email extends Yab_Validator_Abstract {

	const REGEXP = '[\-_\.A-Za-z0-9=]+@[A-Za-z0-9][\-\.A-Za-z0-9]+[A-Za-z0-9]';
	const NOT_VALID = '"$1" is not a valid email address';

	public function _validate($value) {

		if(!preg_match('#^'.self::REGEXP.'$#i', $value))
			$this->addError('NOT_VALID', self::NOT_VALID, $value);
			      
	}

}

// Do not clause PHP tags unless it is really necessary