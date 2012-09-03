<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_CamelCase
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_CamelCase extends Yab_Filter_Abstract {

	public function _filter($value) {

		$filter_pascal_case = new Yab_Filter_PascalCase();

		$value = $filter_pascal_case->filter($value);

		return strtolower(substr($value, 0, 1)).substr($value, 1);

	}

}

// Do not clause PHP tags unless it is really necessary