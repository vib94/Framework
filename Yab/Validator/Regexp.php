<?php
/**
 * Yab Framework
 *
 * @category   Yab_Validator
 * @package    Yab_Validator_Regexp
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Validator_Regexp extends Yab_Validator_Abstract {

	const NOT_MATCH = 'Value does not match with $1';

  public function _validate($value) {

    if(!preg_match($this->get('regexp'), $value))
    	$this->addError('NOT_MATCH', self::NOT_MATCH, $this->get('regexp'));

  }

}

// Do not clause PHP tags unless it is really necessary