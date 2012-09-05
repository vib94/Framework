<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_Xml
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_Xml extends Yab_Filter_Abstract {

	public function _filter($value) {

		return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

	}

}

// Do not clause PHP tags unless it is really necessary