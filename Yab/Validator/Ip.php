<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Ip
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Ip extends Yab_Validator_Abstract {

	const NOT_VALID = 'Value is not a valid ip address';

	public function _validate($value) {

		$firstPartRegexp = '([1-9]|[1-9][0-9]|[1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])';

		$partRegexp = '([0-9]|[1-9][0-9]|[1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])';

		if(!preg_match('#^'.$firstPartRegexp.'\.'.$partRegexp.'\.'.$partRegexp.'\.'.$partRegexp.'$#', $value))
			$this->addError('NOT_VALID', self::NOT_VALID);

	}

}

// Do not clause PHP tags unless it is really necessary