<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Float
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Float extends Yab_Validator_Abstract {

	const NOT_FLOAT = 'Value must be a float';

  public function _validate($value) {

    if((string) $value != (string) floatval($value))
    	$this->addError('NOT_FLOAT', self::NOT_FLOAT);
    	
  }

}

// Do not clause PHP tags unless it is really necessary