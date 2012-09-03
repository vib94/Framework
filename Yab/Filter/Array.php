<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Array
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_Array extends Yab_Filter_Abstract {

	final public function _filter($value) {

		if(is_array($value) || ($value instanceof ArrayAccess))
			return $value;
		  
		if($value === null)
			return array();

		return array($value);

	}

}

// Do not clause PHP tags unless it is really necessary