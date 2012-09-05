<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Instance
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Instance extends Yab_Validator_Abstract {

	const NOT_VALID = 'Value is not an instance of "$1"';

	public function _validate($value) {

		if(!($value instanceof $this->get('of')))
			$this->addError('NOT_VALID', self::NOT_VALID, $this->get('of'));

	}

}

// Do not clause PHP tags unless it is really necessary