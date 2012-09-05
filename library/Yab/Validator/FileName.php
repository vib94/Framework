<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_FileName
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_FileName extends Yab_Validator_Abstract {

	const NOT_VALID = '"$1" is not a valid filename';

	public function _validate($value) {

		if(preg_match('#[/\\:\*\?\|\<\>]#', $value))
			$this->addError('NOT_VALID', self::NOT_VALID, $value);

	}

}

// Do not clause PHP tags unless it is really necessary