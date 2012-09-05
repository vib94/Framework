<?php
/**
 * Yab Framework
 *
 * @category   Yab_Filter
 * @package    Yab_Filter_PascalCase
 * @author     Yann BELLUZZI
 * @copyright  (c) 2010 YBellu
 * @license    http://www.ybellu.com/yab-framework/license.html
 * @link       http://www.ybellu.com/yab-framework 
 */

class Yab_Filter_PascalCase extends Yab_Filter_Abstract {

	public function _filter($value) {

		$separator = $this->has('separator') ? $this->get('separator') : '';
	
		$words = preg_split('#[^a-zA-Z0-9]+#', trim((string) $value));

		$words = array_map('ucfirst', $words);

		return implode($separator, $words);

	}

}

// Do not clause PHP tags unless it is really necessary