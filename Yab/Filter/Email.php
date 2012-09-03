<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Email
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_Email extends Yab_Filter_Abstract {

	public function _filter($value) {

		$email = preg_replace('#^.*?('.Yab_Validator_Email::REGEXP.').*?$#', '$1', $value);
		
		return $email;
	}

}

// Do not clause PHP tags unless it is really necessary