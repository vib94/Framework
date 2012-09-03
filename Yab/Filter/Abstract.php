<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Abstract
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

abstract class Yab_Filter_Abstract extends Yab_Object {

	final public function filter($value) {

		return $this->_filter($value);
	
	}
	
	abstract public function _filter($value);

}

// Do not clause PHP tags unless it is really necessary