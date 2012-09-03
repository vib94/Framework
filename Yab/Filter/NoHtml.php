<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_NoHtml
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_NoHtml extends Yab_Filter_Abstract {

	public function _filter($value) {

		$value = (string) $value;

		$value = strip_tags($value);

		$value = html_entity_decode($value);

		return $value;

	}

}

// Do not clause PHP tags unless it is really necessary